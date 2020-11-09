<?php

if (!isset($_POST['name']) || !isset($_POST['email'])) {
  readfile("apple.html");
  exit;
}

function UUIDv4() {
  $data = PHP_MAJOR_VERSION < 7 ? openssl_random_pseudo_bytes(16) : random_bytes(16);
  $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
  $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
  return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}

$UUID1 = UUIDv4();
$UUID2 = UUIDv4();

$AccountName = $_POST['name'];
$MailAddress = $_POST['email'];

// Profilname
$MailServer    = "mail.example.org";
$DisplayProfil = "Mail Server of SomeOne";
$DisplayName   = "Mail for $AccountName";
$Description   = "Mail: $MailAddress";
$Identifier    = "$MailAddress";
$Organization  = "/TR ;-)";

$data = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
<!DOCTYPE plist PUBLIC \"-//Apple//DTD PLIST 1.0//EN\" \"http://www.apple.com/DTDs/PropertyList-1.0.dtd\">
<plist version=\"1.0\">
<dict>
    <key>PayloadContent</key>
    <array>
	<dict>
	    <key>EmailAccountDescription</key>
	    <string>$Description</string>
	    <key>EmailAccountName</key>
	    <string>$AccountName</string>
	    <key>EmailAccountType</key>
	    <string>EmailTypePOP</string>
	    <key>EmailAddress</key>
	    <string>$MailAddress</string>
	    <key>IncomingMailServerAuthentication</key>
	    <string>EmailAuthPassword</string>
	    <key>IncomingMailServerHostName</key>
	    <string>$MailServer</string>
	    <key>IncomingMailServerPortNumber</key>
	    <integer>995</integer>
	    <key>IncomingMailServerUseSSL</key>
	    <true/>
	    <key>IncomingMailServerUsername</key>
	    <string>$MailAddress</string>
	    <key>IncomingPassword</key>
	    <string></string>
	    <key>OutgoingMailServerAuthentication</key>
	    <string>EmailAuthPassword</string>
	    <key>OutgoingMailServerHostName</key>
	    <string>$MailServer</string>
	    <key>OutgoingMailServerPortNumber</key>
	    <integer>465</integer>
	    <key>OutgoingMailServerUseSSL</key>
	    <true/>
	    <key>OutgoingMailServerUsername</key>
	    <string>$MailAddress</string>
	    <key>OutgoingPasswordSameAsIncomingPassword</key>
	    <true/>
	    <key>PayloadDescription</key>
	    <string>$Description</string>
	    <key>PayloadDisplayName</key>
	    <string>$DisplayName</string>
	    <key>PayloadIdentifier</key>
	    <string>E-Mail $Identifier</string>
	    <key>PayloadOrganization</key>
	    <string>$Organization</string>
	    <key>PayloadType</key>
	    <string>com.apple.mail.managed</string>
	    <key>PayloadUUID</key>
	    <string>$UUID1</string>
	    <key>PayloadVersion</key>
	    <integer>1</integer>
	</dict>
    </array>
    <key>PayloadDisplayName</key>
    <string>$DisplayProfil</string>
    <key>PayloadDescription</key>
    <string>$Description</string>
    <key>PayloadIdentifier</key>
    <string>$Identifier</string>
    <key>PayloadOrganization</key>
    <string>$Organization</string>
    <key>PayloadType</key>
    <string>Configuration</string>
    <key>PayloadUUID</key>
    <string>$UUID2</string>
    <key>PayloadVersion</key>
    <integer>1</integer>
</dict>
</plist>";

/* generate plain config file */
$filenameIn  = "tmp/$UUID1.plain";
$filenameOut = "tmp/$UUID1.signed";
file_put_contents($filenameIn, $data);

/**
 * sign the config file
 * -> be carefull with the path of your private key (should not be reachable from the web)
 */
$cmd = "/usr/bin/openssl smime -sign";
$cmd .= " -signer   certs/cert.pem";
$cmd .= " -inkey    certs/privkey.pem";
$cmd .= " -certfile certs/chain.pem";
$cmd .= " -nodetach -outform der";
$cmd .= " -in $filenameIn -out $filenameOut";
exec($cmd, $out, $rv);

$Date = date('Y-m-d @ H:i:s');
/* this way, you can check, who will call you for assistance ;) */
file_put_contents("tmp/configs.log", "[$rv] $Date: $AccountName <$MailAddress>\n", FILE_APPEND);

if ($rv != 0) {
  echo "Sorry, but this service does not work currently (rv = $rv) :/";
  exit;
}

/* give the user the .mobilconfig file */
header('Content-Type: application/x-apple-aspen-config; chatset=utf-8');
header("Content-Disposition: attachment; filename=\"$MailAddress.mobileconfig\"");
readfile($filenameOut);

/* remove temporary files */
unlink($filenameIn);
unlink($filenameOut);

?>
