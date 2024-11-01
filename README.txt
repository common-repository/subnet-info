=== subnetinfo ===
Contributors: crisvangeel
Donate link: https://www.yellownote.nl/blog/index.php/donate/
Tags: ipv4, ipv6, subnet , subnetinfo, ip, network, calculator,shortcode
Requires at least: 5.0.0
Tested up to: 5.4
Stable tag: 1.0.1
Requires PHP: 5.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Provides detailed information about the IP adress and subnet using a shortcode.

== Description ==

This plugin provides detailed info about an IPv4 or IPv6 subnet.

How do you use it? Place the IP adres and CIDR subnet between [subnetinfo] and [/subnetinfo] brackets.

Use the following format :  [subnetinfo]FE80:0000:0000:0000:0202:B3FF:FE1E:8329/64[/subnetinfo]

or [subnetinfo]192.168.100.10/24[/subnetinfo]

It will be automatically parsed to a nice layout.

The address identification  and all calculation are carried out within the plugin itself.

A link to IANA is created in the results to the corresponding RFC which describes more details about the specific IP range.
For some special IP ranges that are not directly described in an RFC, a link to a relevant website or Wikipedia site is offered.

== Installation ==


1. Upload the folder `subnetinfo` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Start using the shortcode [subnetinfo] [/subnetinfo]

== Frequently Asked Questions ==

= Nothing yet =

Nothing yet

== Screenshots ==

1. Screenshot of shortcode
2. Screenshot of ipv4info
3. Screenshot of ipv6info


== Changelog ==

= 1.0.1

* Tested up to WP5.3

= 1.0 =
* Initial release.


== Arbitrary section ==


== Upgrade Notice == 