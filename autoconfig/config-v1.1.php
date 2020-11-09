<?php header("Content-Type: application/xml"); ?>
<?php
  $servername = $_SERVER["SERVER_NAME"];
  list($subdomain, $domain, $tld) = explode(".", $servername);

  $subdomain = "mail";
?>
<?xml version="1.0" encoding="UTF-8"?>

<clientConfig version="1.1">
  <emailProvider id="<?php echo $subdomain.'.'.$domain.'.'.$tld; ?>">
    <domain><?php echo $_SERVER["SERVER_NAME"]; ?></domain>

    <displayName>Laguna Solutions Mail</displayName>
    <displayShortName>Laguna Mail</displayShortName>

    <incomingServer type="imap">
      <hostname><?php echo $subdomain.'.'.$domain.'.'.$tld; ?></hostname>
      <port>143</port>
      <socketType>STARTTLS</socketType>
      <username>%EMAILADDRESS%</username>
      <authentication>password-cleartext</authentication>
    </incomingServer>

    <outgoingServer type="smtp">
      <hostname><?php echo $subdomain.'.'.$domain.'.'.$tld; ?></hostname>
      <port>587</port>
      <socketType>STARTTLS</socketType>
      <username>%EMAILADDRESS%</username>
      <authentication>password-cleartext</authentication>
    </outgoingServer>

    <documentation url="https://webmail.example.org/">
      <descr lang="en">Generic settings page</descr>
    </documentation>

  </emailProvider>
</clientConfig>
