1. Place Mpower folder into AbanteCart Exntensions folder
2. Go to extensions in AbanteCart admin view and click install on Mpower to install it
3. Set values and turn on Mpower extensions
	##Note first Configurations uses default values which may no longer exits
4. Enable htaccess if not already enabled by renaming htaccess.txt to htaccess in project root or htdocs if project has not been moved 
	to different subfolder
5.Place code within delimiters inside htaccess on lines before "#SEO URL Settings"

<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<

#rewrite url after Mpower successpage redirection appends token to end of return url
RewriteEngine On
RewriteBase /
RewriteCond %{QUERY_STRING} ^(.*callback)[?](token=.*)
RewriteRule ^(.*) $1?%1&%2 

>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>

6.Enable mod_rewrite and change all "AllowOverride None" in x:\Bitnami\abantecart-[version-number]\apache2\conf\httpd.conf

7. Append code inside delimeters to end of x:\bitnami\abantecart-[version-number]\app\abantecart\conf\htaccess.conf

<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<

<Directory "C:/Bitnami/abantecart-1.2.6-1/apps/abantecart/htdocs">
	AllowOverride All
</Directory>

>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>