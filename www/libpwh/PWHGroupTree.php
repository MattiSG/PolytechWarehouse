<?php
    class PWHGroupTree
    {
        const ROOT = -1;
        const CONFIG_TREE = 0;
        const EMAIL_TREE = 1;
        const FORM_TREE = 2;
        const DELIVERY_TREE = 3;
        const STUDENT = 'student';
        const TEACHER = 'teacher';
        
        private $_Tree;
        
        public function __construct()
        {
            $this->_Tree = array();
        }
    
	    public function Html($mode, $user) 
	    {
	        $strbuf = '<div class="tree">';
            $strbuf .= '<div class="node" id="-1">';
		    $strbuf .= $this->PrintTree($this->_Tree, $mode, $user);
		    return $strbuf . '</div></div>';
	    }
	    
	    public function Build($startID)
	    {
	        $this->_Tree = $this->BuildRec($startID);
	    }
	    
	    
	    private function PrintTree($node, $mode, $user)
	    {
		    $length = count($node);
		    $i = 1;
		
		    $empty = $length <= 1;
		    $strbuf = self::PrintGroup($node[0], $empty, $mode, $user) . '<br/>';
		    $id = $node[0]->GetID() == -1 ? 0 : $node[0]->GetID();
		    
		    if($node[0]->GetID() != -1 && $node[0]->GetParentID() == -1)
		    {
		        $strbuf .= '<div class="node" id="' . $id . '" style="display:none">';
		    }
		    else
		    {
		        $strbuf .= '<div class="node" id="' . $id . '">';
		    }
		
		    while($i < $length){
			    $strbuf .= $this->PrintTree($node[$i], $mode, $user);
			    $i++;
		    }
		    return $strbuf .= '</div>';
	    }
	
	    private function PrintGroup($group, $empty, $mode, $user)
	    {
		    $id = $group->GetID() == -1 ? 0 : $group->GetID();
		    $alias = $group->GetName();
		
            
            $children = PWHGroup::GetChildrenOf($group->GetID());
            
            if(count($children) > 0)
            {
		        $strbuf = '<a href="javascript:showhide(\'node' . $id . '\', \'' . $id . '\');"><img src="' . IMG_PATH() . 'minus.png" id="node'. $id .'"></a>';
		    }
		    else
		    {
		        $strbuf = '<a><img src="' . IMG_PATH() . 'square.png" id="node'. $id .'"></a>';
		    }
		    if($id != 0)
		    {
		        if($mode == self::CONFIG_TREE)
		        {
		            $strbuf .= $alias;
		            $strbuf .= '<a href="index.php?page=teacher_group_settings_name&amp;group_id=' . $group->GetID() . '"><img src="' . IMG_PATH() . 'bullet_wrench.png"/></a>';
		            $strbuf .= '<a href="javascript:UserConfirmation(\'' . $group->GetID() . '\');"><img src="' . IMG_PATH() . 'cross.png"/></a>';
		        }
		        else if($mode == self::EMAIL_TREE)
		        {
		            $strbuf .= '<a href="index.php?page=' . $user . '_email_students&amp;group_id='. $group->GetID() . '&amp;index=A">' . $alias . '</a>';
		            
		            $strbuf .= '<a href="mailto:';
		            $strbuf .= $group->GetEmail();
		            $strbuf .= '"><img src="' . IMG_PATH() . 'email.png"/></a>';
		        }
		        else if($mode == self::FORM_TREE)
		        {
		            $strbuf .= $alias . '<input type="checkbox" name="' . $group->GetID() . '"/>';
		        }
		        else if($mode == self::DELIVERY_TREE)
		        {
		            $strbuf .= '<a href="index.php?page=teacher_list_group_deliveries&amp;group_id='. $group->GetID() . '">' . $alias . '</a>';
		            if($group->HasDeliveries())
		            {
		                $strbuf .= '<a href="export/index.php?type=group&amp;group_id=' . $group->GetID() . '&amp;action=show_cal"><img src="img/calendar.png"/></a>';
		            }
		        }
			}
			else
			{
			     $strbuf .= $alias;
			}
			return $strbuf;
	    }
	    
	    private function BuildRec($startID)
	    {
	        $group = new PWHGroup();
		    if($startID == -1)
		    {  
		        $group->SetName('&eacute;cole');
		    }
		    else
		    {
		        $group->Read($startID);
		    }
		    $tree[0] = $group;
		    
		    $i=1;
		    $groups = PWHGroup::GetChildrenOf($startID);
		    foreach($groups as $group) 
		    {
		        $tree[$i] = $this->BuildRec($group->GetID());
		        $i++;
		    }
		    return $tree;
	    }
    }
?>
