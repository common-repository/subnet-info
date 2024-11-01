<?php

if ( ! defined( 'WPINC' ) ) {
	die;
}


//defines the functionality for the location shortcode
class class_subnetinfo_shortcode{
	
	//on initialize
	public function __construct(){
		add_action('init', array($this,'subnetinfo_shortcodes')); //shortcodes
		//include( plugin_dir_path( __FILE__ ) . 'BigInteger.php');//Include BigInter Lib.
	
	}

	//location shortcode
	public function subnetinfo_shortcodes(){
		add_shortcode('subnetinfo', array($this,'subnetinfo_shortcode_output'));
	}
	

	public function subnetinfo_shortcode_output($atts, $content = null){
		
	
	//Add Start Tag
	$format = "\n<!-- Start subnetinfo " . SUBNETINFO_VERSION . " -->\n";
	//$format .= "\n<pre>\n";
	$format .= "\n<span class='subnetinfo'>";
	
	//Strip the Subnet
	$ip = explode("/",$content);
	
	$isipv4 = false;
	$ipaddr = array();
	$additional ="";
	
	//Check if valid IP and subnet and sanitze input
	$ipaddr = strip_tags(trim($ip[0]));
	if (isset($ip[1])) { $subnet = strip_tags(trim($ip[1])); } else { return "<p><b>[subnetinfo]</b> : No CIDR Notation subnet provided. For example 192.168.0.10/24</p>"; }

	if (!$in_addr = @inet_pton($ipaddr)) { return "<p><b>[subnetinfo]</b> : Invalid IP address or format.</p>"; }

	if (preg_match("/\A(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\z/", $ipaddr)) { $isipv4=true; } else {$isipv4 = false; }
	
	
	//Handle IPv4 part
	if ($isipv4 === TRUE) { 
	
		
				$subnetmask = subnetinfo_ipv4_CIDRtoMask($subnet);
				$network = subnetinfo_ipv4_networkaddress($ipaddr,$subnetmask);
				$networkbroadcast = subnetinfo_ipv4_networkbroadcastaddress($ipaddr,$subnetmask);
				$wildcard = long2ip( ~ip2long($subnetmask) );
				
				//calculate first usable address
				$ip_host_first = ((~ip2long($subnetmask)) & ip2long($ipaddr));
				$hostmin = long2ip((ip2long($ipaddr) ^ $ip_host_first) + 1);

				//calculate last usable address
				$hostmax = long2ip(ip2long($network) | ~ip2long($subnetmask) -1);
				
				//Calculate amount of useable hosts
				$hosts = (ip2long($hostmax) - ip2long($hostmin));
				
				//Find Network class
				$first_octet = ip2long($ipaddr) >>24 & 255;
				
				//Binary IP
				$binip = sprintf("%032b",ip2long($ipaddr));
				$binsubnetmask = sprintf("%032b",ip2long($subnetmask));

				//IP In Hex format
				$hexip =sprintf("0x%08X",ip2long($ipaddr));

				
				//Determine Network Class
				if ($first_octet >= 1 && $first_octet <= 126) { $networkclass = "A"; }
				if ($first_octet === 127) { $networkclass = "A"; }
				if ($first_octet >= 128 && $first_octet <= 191) { $networkclass = "B"; }
				if ($first_octet >= 192 && $first_octet <= 223) { $networkclass = "C"; }
				if ($first_octet >= 224 && $first_octet <= 239) { $networkclass = "D"; }
				if ($first_octet >= 240 && $first_octet <= 255) { $networkclass = "E"; }
				
				//IANA Special addresses https://www.iana.org/assignments/iana-ipv4-special-registry/iana-ipv4-special-registry.xhtml
				if (subnetinfo_ipv4_ip_in_range($ipaddr,"0.0.0.0/0")) { $additional = "(Internet Address <a href='http://www.iana.org/go/rfc1122' target='_blank'>RFC1122</a> )"; }
				if (subnetinfo_ipv4_ip_in_range($ipaddr,"10.0.0.0/8")) { $additional = "(Private Network <a href='http://www.iana.org/go/rfc1918' target='_blank'>RFC1918</a> )"; }
				if (subnetinfo_ipv4_ip_in_range($ipaddr,"100.64.0.0/10")) { $additional = "(Shared Address Space <a href='http://www.iana.org/go/rfc6598' target='_blank'>RFC6598</a> )"; }
				if (subnetinfo_ipv4_ip_in_range($ipaddr,"127.0.0.0/8")) { $additional = "(Loopback <a href='http://www.iana.org/go/rfc1122' target='_blank'>RFC1122</a> )"; }
				if (subnetinfo_ipv4_ip_in_range($ipaddr,"169.254.0.0/16")) { $additional = "(APIPA / Link Local <a href='http://www.iana.org/go/rfc3927' target='_blank'>RFC3927</a> )"; }
				if (subnetinfo_ipv4_ip_in_range($ipaddr,"172.16.0.0/12")) { $additional = "(Private Network <a href='http://www.iana.org/go/rfc1918' target='_blank'>RFC1918</a> )"; }
				if (subnetinfo_ipv4_ip_in_range($ipaddr,"192.0.0.0/24")) { $additional = "(IETF Protocol Assignments <a href='http://www.iana.org/go/rfc6890' target='_blank'>RFC6890</a> )"; }
				if (subnetinfo_ipv4_ip_in_range($ipaddr,"192.0.0.0/29")) { $additional = "(IPv4 Service Continuity Prefix <a href='http://www.iana.org/go/rfc7335' target='_blank'>RFC7335</a> )"; }
				if (subnetinfo_ipv4_ip_in_range($ipaddr,"192.0.0.8/32")) { $additional = "(IPv4 Dummy Address <a href='http://www.iana.org/go/rfc7600' target='_blank'>RFC7600</a> )"; }
				if (subnetinfo_ipv4_ip_in_range($ipaddr,"192.0.0.9/32")) { $additional = "(Port Control Protocol Anycast <a href='http://www.iana.org/go/rfc7723' target='_blank'>RFC7723</a> )"; }
				if (subnetinfo_ipv4_ip_in_range($ipaddr,"192.0.0.170/32")) { $additional = "(NAT64/DNS64 Discovery <a href='http://www.iana.org/go/rfc7050' target='_blank'>RFC7050</a> )"; }
				if (subnetinfo_ipv4_ip_in_range($ipaddr,"192.0.0.171/32")) { $additional = "(NAT64/DNS64 Discovery <a href='http://www.iana.org/go/rfc7050' target='_blank'>RFC7050</a> )"; }
				if (subnetinfo_ipv4_ip_in_range($ipaddr,"192.0.2.0/24")) { $additional = "(Documentation (TEST-NET-1) <a href='http://www.iana.org/go/rfc5737' target='_blank'>RFC5737</a> )"; }
				if (subnetinfo_ipv4_ip_in_range($ipaddr,"192.31.196.0/24")) { $additional = "(AS112-v4 <a href='http://www.iana.org/go/rfc7535' target='_blank'>RFC7535</a> )"; }
				if (subnetinfo_ipv4_ip_in_range($ipaddr,"192.52.193.0/24")) { $additional = "(AMT <a href='http://www.iana.org/go/rfc7450' target='_blank'>RFC7450</a> )"; }
				if (subnetinfo_ipv4_ip_in_range($ipaddr,"192.88.99.0/24")) { $additional = "(Deprecated (6to4 Relay Anycast) <a href='http://www.iana.org/go/rfc7526' target='_blank'>RFC7526</a> )"; }
				if (subnetinfo_ipv4_ip_in_range($ipaddr,"192.168.0.0/16")) { $additional = "(Private Network <a href='http://www.iana.org/go/rfc1918' target='_blank'>RFC1918</a> )"; }
				if (subnetinfo_ipv4_ip_in_range($ipaddr,"198.18.0.0/15")) { $additional = "(Benchmarking <a href='http://www.iana.org/go/rfc2544' target='_blank'>RFC2544</a> )"; }
				if (subnetinfo_ipv4_ip_in_range($ipaddr,"198.51.100.0/24")) { $additional = "(Documentation (TEST-NET-2) <a href='http://www.iana.org/go/rfc5737' target='_blank'>RFC5737</a> )"; }
				if (subnetinfo_ipv4_ip_in_range($ipaddr,"203.0.113.0/24")) { $additional = "(Documentation (TEST-NET-3) <a href='http://www.iana.org/go/rfc5737' target='_blank'>RFC5737</a> )"; }
				if (subnetinfo_ipv4_ip_in_range($ipaddr,"255.255.255.255/32")) { $additional = "(Limited Broadcast <a href='http://www.iana.org/go/rfc8190' target='_blank'>RFC8190</a> )"; }

				//Special addresses own additions
				if (subnetinfo_ipv4_ip_in_range($ipaddr,"44.0.0.0/8")) { $additional = "(AMPRNet <a href='https://en.wikipedia.org/wiki/AMPRNet' target='_blank'>Wiki</a> )"; }
				
				
				//Multicast addresses https://www.iana.org/assignments/multicast-addresses/multicast-addresses.xhtml
				if (subnetinfo_ipv4_ip_in_range($ipaddr,"224.0.0.0/8")) { $additional = "(IP Multicasting <a href='http://www.iana.org/go/rfc1112' target='_blank'>RFC1112</a> )"; }
				if (subnetinfo_ipv4_ip_in_range($ipaddr,"225.0.0.0/8")) { $additional = "(IP Multicasting <a href='http://www.iana.org/go/rfc1112' target='_blank'>RFC1112</a> )"; }
				if (subnetinfo_ipv4_ip_in_range($ipaddr,"226.0.0.0/8")) { $additional = "(IP Multicasting <a href='http://www.iana.org/go/rfc1112' target='_blank'>RFC1112</a> )"; }
				if (subnetinfo_ipv4_ip_in_range($ipaddr,"227.0.0.0/8")) { $additional = "(IP Multicasting <a href='http://www.iana.org/go/rfc1112' target='_blank'>RFC1112</a> )"; }
				if (subnetinfo_ipv4_ip_in_range($ipaddr,"228.0.0.0/8")) { $additional = "(IP Multicasting <a href='http://www.iana.org/go/rfc1112' target='_blank'>RFC1112</a> )"; }
				if (subnetinfo_ipv4_ip_in_range($ipaddr,"229.0.0.0/8")) { $additional = "(IP Multicasting <a href='http://www.iana.org/go/rfc1112' target='_blank'>RFC1112</a> )"; }
				if (subnetinfo_ipv4_ip_in_range($ipaddr,"230.0.0.0/8")) { $additional = "(IP Multicasting <a href='http://www.iana.org/go/rfc1112' target='_blank'>RFC1112</a> )"; }
				if (subnetinfo_ipv4_ip_in_range($ipaddr,"231.0.0.0/8")) { $additional = "(IP Multicasting <a href='http://www.iana.org/go/rfc1112' target='_blank'>RFC1112</a> )"; }
				if (subnetinfo_ipv4_ip_in_range($ipaddr,"232.0.0.0/8")) { $additional = "(IP Multicasting <a href='http://www.iana.org/go/rfc1112' target='_blank'>RFC1112</a> )"; }
				if (subnetinfo_ipv4_ip_in_range($ipaddr,"233.0.0.0/8")) { $additional = "(IP Multicasting <a href='http://www.iana.org/go/rfc1112' target='_blank'>RFC1112</a> )"; }
				if (subnetinfo_ipv4_ip_in_range($ipaddr,"234.0.0.0/8")) { $additional = "(IP Multicasting <a href='http://www.iana.org/go/rfc1112' target='_blank'>RFC1112</a> )"; }
				if (subnetinfo_ipv4_ip_in_range($ipaddr,"235.0.0.0/8")) { $additional = "(IP Multicasting <a href='http://www.iana.org/go/rfc1112' target='_blank'>RFC1112</a> )"; }
				if (subnetinfo_ipv4_ip_in_range($ipaddr,"235.0.0.0/8")) { $additional = "(IP Multicasting <a href='http://www.iana.org/go/rfc1112' target='_blank'>RFC1112</a> )"; }
				if (subnetinfo_ipv4_ip_in_range($ipaddr,"236.0.0.0/8")) { $additional = "(IP Multicasting <a href='http://www.iana.org/go/rfc1112' target='_blank'>RFC1112</a> )"; }
				if (subnetinfo_ipv4_ip_in_range($ipaddr,"237.0.0.0/8")) { $additional = "(IP Multicasting <a href='http://www.iana.org/go/rfc1112' target='_blank'>RFC1112</a> )"; }
				if (subnetinfo_ipv4_ip_in_range($ipaddr,"238.0.0.0/8")) { $additional = "(IP Multicasting <a href='http://www.iana.org/go/rfc1112' target='_blank'>RFC1112</a> )"; }
				if (subnetinfo_ipv4_ip_in_range($ipaddr,"239.0.0.0/8")) { $additional = "(IP Multicasting <a href='http://www.iana.org/go/rfc1112' target='_blank'>RFC1112</a> )"; }
				if (subnetinfo_ipv4_ip_in_range($ipaddr,"240.0.0.0/4")) { $additional = "(Future Use <a href='http://www.iana.org/go/rfc1112' target='_blank'>RFC1112</a> )"; }
				
					
				
				
				
				$format .= "Address IPv4: \t".$ipaddr." / ".$subnet;
				$format .= "\n";
				$format .= "Netmask: \t".$subnetmask;
				$format .= "\n";
				$format .= "Decimal IP\t".ip2long($ipaddr);
				$format .= "\n";
				$format .= "Heximal IP\t".$hexip;
				$format .= "\n";
				$format .= "Binary IP\t".$binip;
				$format .= "\n";
				$format .= "Binary Subnet\t".$binsubnetmask;
				$format .= "\n";
				$format .= "Wildcard: \t".$wildcard;
				$format .= "\n";
				$format .= "Network: \t".$network;
				$format .= "\n";
				$format .= "Broadcast: \t".$networkbroadcast;
				$format .= "\n";
				$format .= "HostMin: \t".$hostmin;
				$format .= "\n";
				$format .= "HostMax: \t".$hostmax;
				$format .= "\n";
				$format .= "Usable hosts: \t".$hosts;
				$format .= "\n";
				$format .= "Network Class: \t".$networkclass." ".$additional;
				
				
				
					
	}

	//Handle IPv6 part
	if ($isipv4 === FALSE) { 
	
			$charHost = inet_pton($ipaddr);
			$netmask = subnetinfo_Cdr2Char($subnet);
				
			
			// Single host mask used for hostmin and hostmax bitwise operations
			$charHostMask = substr(subnetinfo_cdr2Char(127),-strlen($charHost));

			$wildcard = ~$netmask; // Supernet wildcard mask
			$charNet = $charHost & $netmask; // Supernet network address
			$networkbroadcast = $charNet | ~$netmask; // Supernet broadcast
			$hostmin = $charNet | ~$charHostMask; // Minimum host
			$hostmax = $networkbroadcast & $charHostMask; // Maximum host
			
			//IP In Hex format
			$hexip = subnetinfo_char2Hex($charHost);
			$binip = subnetinfo_hex2Bin($hexip);
			//$decip = inet_ptoi($ipaddr);
			
			
				
			
			//IANA Special addresses https://www.iana.org/assignments/iana-ipv6-special-registry/iana-ipv6-special-registry.xhtml & https://www.iana.org/assignments/ipv6-address-space/ipv6-address-space.xhtml
			if (subnetinfo_ipv6_ip_in_range($ipaddr,"::1/128")) { $additional = "Loopback Address <a href='http://www.iana.org/go/rfc4291' target='_blank'>RFC4291</a>"; }
			if (subnetinfo_ipv6_ip_in_range($ipaddr,"::/128")) { $additional = "Unspecified address <a href='http://www.iana.org/go/rfc4291' target='_blank'>RFC4291</a>"; }
			if (subnetinfo_ipv6_ip_in_range($ipaddr,"::ffff:0:0/96")) { $additional = "IPv4-mapped Address <a href='http://www.iana.org/go/rfc4291' target='_blank'>RFC4291</a>"; }
			
			if (subnetinfo_ipv6_ip_in_range($ipaddr,"0000::/8")) { $additional = "Reserved by IETF <a href='http://www.iana.org/go/rfc4291' target='_blank'>RFC4291</a>"; }
			if (subnetinfo_ipv6_ip_in_range($ipaddr,"0064:ff9b::/96")) { $additional = "IPv4-IPv6 Translat. <a href='http://www.iana.org/go/rfc6052' target='_blank'>RFC6052</a>"; }
			if (subnetinfo_ipv6_ip_in_range($ipaddr,"0064:ff9b:1::/48")) { $additional = "IPv4-IPv6 Translat. <a href='http://www.iana.org/go/rfc8215' target='_blank'>RFC8215</a>"; }
			
			if (subnetinfo_ipv6_ip_in_range($ipaddr,"0100::/8")) { $additional = "Reserved by IETF <a href='http://www.iana.org/go/rfc4291' target='_blank'>RFC4291</a>"; }
			if (subnetinfo_ipv6_ip_in_range($ipaddr,"0100::/64")) { $additional = "Discard-Only Address Block <a href='http://www.iana.org/go/rfc6666' target='_blank'>RFC6666</a>"; }
			if (subnetinfo_ipv6_ip_in_range($ipaddr,"0200::/7")) { $additional = "Reserved by IETF <a href='http://www.iana.org/go/rfc4048' target='_blank'>RFC4048</a>"; }
			if (subnetinfo_ipv6_ip_in_range($ipaddr,"0400::/6")) { $additional = "Reserved by IETF <a href='http://www.iana.org/go/rfc4291' target='_blank'>RFC4291</a>"; }
			if (subnetinfo_ipv6_ip_in_range($ipaddr,"0800::/5")) { $additional = "Reserved by IETF <a href='http://www.iana.org/go/rfc4291' target='_blank'>RFC4291</a>"; }
			if (subnetinfo_ipv6_ip_in_range($ipaddr,"1000::/4")) { $additional = "Reserved by IETF <a href='http://www.iana.org/go/rfc4291' target='_blank'>RFC4291</a>"; }
						
			if (subnetinfo_ipv6_ip_in_range($ipaddr,"2000::/3")) { $additional = "Global Unicast <a href='http://www.iana.org/go/rfc2928' target='_blank'>RFC2928</a>"; }
			if (subnetinfo_ipv6_ip_in_range($ipaddr,"2001::/23")) { $additional = "IETF Protocol Assignments <a href='http://www.iana.org/go/rfc2928' target='_blank'>RFC2928</a>"; }
			if (subnetinfo_ipv6_ip_in_range($ipaddr,"2001::/32")) { $additional = "TEREDO <a href='http://www.iana.org/go/rfc4380' target='_blank'>RFC4380</a> )"; }
			
			if (subnetinfo_ipv6_ip_in_range($ipaddr,"2001:1::1/128")) { $additional = " Port Control Protocol Anycast <a href='http://www.iana.org/go/rfc7723' target='_blank'>RFC7723</a>"; }
			if (subnetinfo_ipv6_ip_in_range($ipaddr,"2001:1::2/128")) { $additional = "Traversal Using Relays around NAT Anycast <a href='http://www.iana.org/go/rfc8155' target='_blank'>RFC8155</a>"; }
			if (subnetinfo_ipv6_ip_in_range($ipaddr,"2001:2::/48")) { $additional = "Benchmarking <a href='http://www.iana.org/go/rfc5180' target='_blank'>RFC5180</a>"; }
			if (subnetinfo_ipv6_ip_in_range($ipaddr,"2001:3::/32")) { $additional = "AMT <a href='http://www.iana.org/go/rfc7450' target='_blank'>RFC7450</a>"; }
			if (subnetinfo_ipv6_ip_in_range($ipaddr,"2001:4:112::/48")) { $additional = "AS112-v6 <a href='http://www.iana.org/go/rfc7535' target='_blank'>RFC7535</a>"; }
			
			if (subnetinfo_ipv6_ip_in_range($ipaddr,"2001:5::/32")) { $additional = "EID Space for LISP (Managed by RIPE NCC) <a href='http://www.iana.org/go/rfc7954' target='_blank'>RFC7954</a>"; }
			if (subnetinfo_ipv6_ip_in_range($ipaddr,"2001:10::/28")) { $additional = "Deprecated (previously ORCHID) <a href='http://www.iana.org/go/rfc4843' target='_blank'>RFC4843</a>"; }
			if (subnetinfo_ipv6_ip_in_range($ipaddr,"2001:20::/28")) { $additional = "ORCHIDv2 <a href='http://www.iana.org/go/rfc7343' target='_blank'>RFC7543</a>"; }
			if (subnetinfo_ipv6_ip_in_range($ipaddr,"2001:db8::/32")) { $additional = "Documentation <a href='http://www.iana.org/go/rfc3849' target='_blank'>RFC3849</a>"; }
			if (subnetinfo_ipv6_ip_in_range($ipaddr,"2002::/16")) { $additional = "6to4 <a href='http://www.iana.org/go/rfc3056' target='_blank'>RFC3056</a>"; }
			
			if (subnetinfo_ipv6_ip_in_range($ipaddr,"2620:4f:8000::/48")) { $additional = "Direct Delegation AS112 Service <a href='http://www.iana.org/go/rfc7534' target='_blank'>RFC7534</a>"; }

			if (subnetinfo_ipv6_ip_in_range($ipaddr,"4000::/3")) { $additional = "Reserved by IETF <a href='http://www.iana.org/go/rfc4291' target='_blank'>RFC4291</a>"; }
			if (subnetinfo_ipv6_ip_in_range($ipaddr,"6000::/3")) { $additional = "Reserved by IETF <a href='http://www.iana.org/go/rfc4291' target='_blank'>RFC4291</a>"; }
			if (subnetinfo_ipv6_ip_in_range($ipaddr,"8000::/3")) { $additional = "Reserved by IETF <a href='http://www.iana.org/go/rfc4291' target='_blank'>RFC4291</a>"; }
			if (subnetinfo_ipv6_ip_in_range($ipaddr,"a000::/3")) { $additional = "Reserved by IETF <a href='http://www.iana.org/go/rfc4291' target='_blank'>RFC4291</a>"; }
			if (subnetinfo_ipv6_ip_in_range($ipaddr,"c000::/3")) { $additional = "Reserved by IETF <a href='http://www.iana.org/go/rfc4291' target='_blank'>RFC4291</a>"; }
			if (subnetinfo_ipv6_ip_in_range($ipaddr,"e000::/4")) { $additional = "Reserved by IETF <a href='http://www.iana.org/go/rfc4291' target='_blank'>RFC4291</a>"; }
			
			if (subnetinfo_ipv6_ip_in_range($ipaddr,"f800::/6")) { $additional = "Reserved by IETF <a href='http://www.iana.org/go/rfc4291' target='_blank'>RFC4291</a>"; }
			if (subnetinfo_ipv6_ip_in_range($ipaddr,"fc00::/7")) { $additional = "Unique-Local <a href='http://www.iana.org/go/rfc4193' target='_blank'>RFC4193</a>"; }
			if (subnetinfo_ipv6_ip_in_range($ipaddr,"fe80::/10")) { $additional = "Link-Local Unicast <a href='http://www.iana.org/go/rfc4291' target='_blank'>RFC4291</a>"; }
			if (subnetinfo_ipv6_ip_in_range($ipaddr,"fec0::/10")) { $additional = "Reserved by IETF <a href='http://www.iana.org/go/rfc3879' target='_blank'>RFC3879</a>"; }
			if (subnetinfo_ipv6_ip_in_range($ipaddr,"ff00::/8")) { $additional = "Multicast <a href='http://www.iana.org/go/rfc4291' target='_blank'>RFC4291</a>"; }
		
			
			
	
			
				
				
				$format .= "Address IPv6: \t".$ipaddr." / ".$subnet;
				$format .= "\n";
				$format .= "Netmask: \t".subnetinfo_inet6_expand(inet_ntop($netmask));
				$format .= "\n";
				$format .= "Wildcard: \t".subnetinfo_inet6_expand(inet_ntop($wildcard));
				$format .= "\n";
				$format .= "HostMin: \t".subnetinfo_inet6_expand(inet_ntop($hostmin));
				$format .= "\n";
				$format .= "HostMax: \t".subnetinfo_inet6_expand(inet_ntop($hostmax));
				$format .= "\n";
				$format .= "IP Addr. full: \t".subnetinfo_inet6_expand($ipaddr);
				$format .= "\n";
				$format .= "Heximal ID: \t0x".$hexip;
				//$format .= "\n";
				//$format .= "Integer ID: \t".$decip;
				$format .= "\n";
				$format .= "Type address: \t".$additional;
				//$format .= "\n";
				//$format .= "Base 85 ID: \t".to_base85($charHost);
				
				
	
		
	
	}
	
	//Add end tag
	//$format .= "\n</pre>\n";
	$format .= "\n</span>\n";
	$format .=	"\n<!-- End subnetinfo " . SUBNETINFO_VERSION . " -->\n";
	return $format;
	
	
	}
}
/*-----------------------------------------------------------------------------------------------------------------------------*/







/* 
 * IPv4 Specific Functions 
 */

function subnetinfo_ipv4_CIDRtoMask($int) {
    return long2ip(-1 << (32 - (int)$int));
}

function subnetinfo_ipv4_networkaddress($ip,$netmask) {
	$ip = ip2long($ip);
	$netmask = ip2long($netmask);
	return long2ip((int)$ip & (int)$netmask);
}

function subnetinfo_ipv4_networkbroadcastaddress($ip,$netmask) {
	$ip = ip2long($ip);
	$netmask = ip2long($netmask);
	return long2ip((int)$ip | (~(int)$netmask));
}

function subnetinfo_ipv4_ip_in_range($ip, $range)
{
    if (strpos($range, '/') == false) {
    	$range .= '/32';
    }
    list($range, $netmask) = explode('/', $range, 2);
    $ip_decimal = ip2long($ip);
    $range_decimal = ip2long($range);
    $wildcard_decimal = pow(2, (32 - $netmask)) - 1;
    $netmask_decimal = ~ $wildcard_decimal;
    return (($ip_decimal & $netmask_decimal) == ($range_decimal & $netmask_decimal));
}

/* 
 * IPv6 Specific Functions 
 */

function subnetinfo_cdr2Bin ($cdrin,$len=4){
	if ( $len > 4 || $cdrin > 32 ) { // Are we ipv6?
		return str_pad(str_pad("", $cdrin, "1"), 128, "0");
	} else {
	  return str_pad(str_pad("", $cdrin, "1"), 32, "0");
	}
}

function subnetinfo_bin2Cdr ($binin){
	return strlen(rtrim($binin,"0"));
}

function subnetinfo_cdr2Char ($cdrin,$len=4){
	$hex = subnetinfo_bin2Hex(subnetinfo_cdr2Bin($cdrin,$len));
	return subnetinfo_hex2Char($hex);
}

function subnetinfo_char2Cdr ($char){
	$bin = subnetinfo_hex2Bin(subnetinfo_char2Hex($char));
	return subnetinfo_bin2Cdr($bin);
}

function subnetinfo_hex2Char($hex){
	return pack('H*',$hex);
}

function subnetinfo_char2Hex($char){
	$hex = unpack('H*',$char);
	return array_pop($hex);
}

function subnetinfo_hex2Bin($hex){
  $bin='';
  for($i=0;$i<strlen($hex);$i++)
    $bin.=str_pad(decbin(hexdec($hex{$i})),4,'0',STR_PAD_LEFT);
  return $bin;
}

function subnetinfo_bin2Hex($bin){
  $hex='';
  for($i=strlen($bin)-4;$i>=0;$i-=4)
    $hex.=dechex(bindec(substr($bin,$i,4)));
  return strrev($hex);
}

function subnetinfo_inet6_expand($addr)
{
    /* Check if there are segments missing, insert if necessary */
    if (strpos($addr, '::') !== false) {
        $part = explode('::', $addr);
        $part[0] = explode(':', $part[0]);
        $part[1] = explode(':', $part[1]);
        $missing = array();
        for ($i = 0; $i < (8 - (count($part[0]) + count($part[1]))); $i++)
            array_push($missing, '0000');
        $missing = array_merge($part[0], $missing);
        $part = array_merge($missing, $part[1]);
    } else {
        $part = explode(":", $addr);
    } // if .. else
    /* Pad each segment until it has 4 digits */
    foreach ($part as &$p) {
        while (strlen($p) < 4) $p = '0' . $p;
    } // foreach
    unset($p);
    /* Join segments */
    $result = implode(':', $part);
    /* Quick check to make sure the length is as expected */ 
    if (strlen($result) == 39) {
        return $result;
    } else {
        return false;
    }
}

	
function subnetinfo_iPv6MaskToByteArray($subnetMask) {
  $addr = str_repeat("f", $subnetMask / 4);
  switch ($subnetMask % 4) {
    case 0:
      break;
    case 1:
      $addr .= "8";
      break;
    case 2:
      $addr .= "c";
      break;
    case 3:
      $addr .= "e";
      break;
  }
  $addr = str_pad($addr, 32, '0');
  $addr = pack("H*" , $addr);
  return $addr;
}

//Check if IPv6 Addres is in CIDR Range
function subnetinfo_ipv6_ip_in_range($address, $subnetAddress) {
	
	$ip = explode("/",$subnetAddress);
	$mask = $ip[1];
	$ipsubnet = inet_pton($ip[0]);
	
	$address = inet_pton($address);
	$binMask = subnetinfo_iPv6MaskToByteArray($mask);
	return ($address & $binMask) == $ipsubnet;
}

//Register shortcode in wordpress
$class_subnetinfo_shortcode = new class_subnetinfo_shortcode;



?>
