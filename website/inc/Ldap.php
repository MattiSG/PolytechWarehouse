<?php
class Ldap
{
  private static function getCryptedPassword($login)
  {
    $ldap_id = ldap_connect(LDAP_SERVER);
    ldap_set_option($ldap_id,LDAP_OPT_PROTOCOL_VERSION,3);
    $binding = ldap_bind($ldap_id); 
    $search_results = ldap_search($ldap_id,LDAP_ROOT,"(uid=$login)");
    $results = ldap_get_entries($ldap_id, $search_results);
    $line = explode("{crypt}",$results[0]["userpassword"][0]);
    return $line[1];
    ldap_unbind($ldap_id);
  }

  public static function checkPassword($login,$plainTextPassword)
  {
    if (BROKEN_GLASS === true)
      return true;
    $expected = self::getCryptedPassword($login);
    if (crypt($plainTextPassword,$expected) == $expected)
      return true;
    else
      return false;
  }

}

?>