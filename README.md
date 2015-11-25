## WP Advanced Importer Plugin ##

**Contributors:** smackcoders

**Donate link:** http://www.smackcoders.com/donate.html

**Tags:** batch, import, plugin, admin, xml, importer, data, backup,restore

**Requires at least:** 4.3

**Tested up to:** 4.3.1

**Stable tag:** 2.1.1

**Version:** 2.1.1

**Author:** smackcoders

**Author URI:** http://profiles.WordPress.org/smackcoders/

**License:** GPLv2 or later

An importer with better inline and featured image handling with Post, Page, Custom Post, User and eShop import.

#### Description ####

Advanced importer enables to import a bulk XML file with any number of records. It enables to import Post, Page, Custom Post, Users and eShop. It also supports Custom Post Type UI plugin and SEO import with All in One SEO plugin. The import process is executed in few simple clicks. The file is imported perfectly without any broken image or blank page issue. 

**Highlights**

* eShop products can be imported.
* Advanced media handling with inline image import with shortcodes.
* Dazzling dashboard to grasp the details of import process in a blink of an eye.
* All in One SEO fields can be imported along with Post, Page, Custom Post and eShop.
* Reduces shifting of screen by providing an option for Custom Field registration on the flow. 

**Menu**

The plugin has three menus. Dashboard, Import and Settings. The Dashboard holds two graphical representation of import history. The 'Importer's Activity' chart shows the import history over a year and the 'Import Statistics' shows the imported module details in a pie chart. The Import menu enables to carry out the import process. The Settings menu enables to restrict the author and editor import. It enables to handle error by enabling debug mode. The php.ini settings enables to cross check the minimum requirement with the server configurations.

**Procedure**

Step-1 Upload 
* Upload the XML to be imported. 
* Click on 'Next' to proceed import. 

Step-2 Mapping 
* The WP fields and the XML nodes are listed, map the WP fields with the corresponding nodes. 
* If the import module is Custom Post, choose the custom post type in the dropdown. 
* The SEO fields is displayed when All in One SEO is activated in plugin list.

Step -3 Security and Performance 
* To eliminate duplicate content, choose the duplicate content and title option.
* Specify the number of server requests based on the server configurations. * Click on 'Import now' to proceed import.
* Now the log for the current import is generated with both Admin view and Web view.
 


This plugin will help all the way to import XML without much pain. There is no room to be left in dark with broken import etc.

To more about the product, visit [Documentation](https://www.wpultimatecsvimporter.com/documentation/all-import/wp-advanced-importer/getting-started/)

For technical support and feature request,visit  [Smackcoders support](https://smackcoders.freshdesk.com)

To know more about our PRO product, visit [WP Ultimate CSV Importer](https://www.wpultimatecsvimporter.com)

#### Installation ####

1. Extract the wp-advanced-importer.zip in wordpress/wp-content/plugins using FTP or through plugin install in wp-admin.
2. Create folder named “uploads”  within wp-content.
3. Give 755 permission for both wp-content and uploads folder. (i.e)
	In terminal run the command, chmod 755 -R wp-content
4. Activate the plugin in WordPress plugin list.

#### Screenshots ####

1. WP Advanced Importer with ultimate user friendly dashboard features.
2. XML upload view of the Advance Importer.
3. Advanced mapping section with 'Add Custom field' button, Post type and Post status dropdown .
4. Security and Performance improvement and duplicate title and content check.
5. Advanced log of the current import process with Admin view and Web View.
6. Settings of the Advanced importer to know the php.ini details.

#### Frequently Asked Questions ####

1. Is there any limitation on file size?
No, there is no limitation on file size. The file can have any number of records and the performance is based on the server configurations.

2. Can All in One SEO fields be imported along with eShop?
yes, All in One SEO fields can be imported along with the eShop products.

3. Can we register any number of Custom fields during import?
yes, any number of fields can be registered on the flow of import process.

4. Can we include external URL for image import?
Featured image can be imported from external URL but inline image can be populated only through shortcode.

5. Is there any format specification for the image folder?
Yes, the image folder need to be in zip format and it should contain all the images specified in the shortcode. 

6. How to enable the import button?
The import button will be enabled after uploading the XML file which is to be imported. If not, verify whether the file is properly formatted.



#### Change log ####

**2.1.1**
* Modified: Enhanced dashboard view.

**2.1**
* Added: eShop product and All in One SEO fields import.
* Added: Create new WordPress Custom Field on the flow of import.
* Added: Advance media handling with shortcode.
* Added: Author and editor import restriction.
* Added: Error handling with debug mode.
* Added: Dashboard chart to represent import details. 
* Modified: New simplified UI to have a better experience.

**2.0.7**
* Fixed: apache_get_modules() problem is fixed.

**2.0.6**
* Fixed: ../../../../wp-load.php is removed and WordPress ajax call is used.
* Fixed: Removed the wp-content that are directly used in some files.
* Added: Checked the ABSPATH in each file.
* Fixed: Upload media zip option issue is fixed.
* Fixed: Featured image issue is fixed.
* Fixed: All warnings are fixed.	
* Fixed: All minor bugs are fixed.
* Fixed: All typeErrors are fixed.

**2.0.5**
* Fixed: Reported forum bugs.
* Fixed: warnings fixed
* Added: WordPress 4.2.2 Compatibility checked.

**2.0.4**
* Fixed: Reported forum bugs.
* Added: WordPress 4.1.1 Compatibility checked.
  
**2.0.2**
* Added: WordPress 4.1 compatibility checked.
* Fixed: Importing image related issues.
* Added: Securities and Performance tab.

**2.0.1**
* Modified: Menu Order changes added to avoid the blank page issues.
* Added: Dynamic Debug mode enable/disable feature in settings module.

**2.0**
* Added support for import XML author.
* Added support for advanced user mapping allows to assign any desired user or create new user.
* Advanced content mapping, user can selectively import their posts attributes and they can edit the mapping by selecting the post types.
* Advanced Media handling user can download their attachment by XML attachment URL or else they can upload the media as a portable zip 
* Added support for categories, Tags, post format, WordPress custom fields, featured images, comments etc. 

**1.2.1** 
* Hot security fix added.

**1.2.0** 
* Complete revamp of code based on skinny MVC.
* Improved performance.
* Complete change in UI. 
* Logs added.
* Duplicate detection added.

**1.1.0**
* Added: Comments import feature added for posts,pages and custom posts.

**1.0.0**	
* Initial release version. Tested and found to work well without any issues.

#### Upgrade Notice ####

**2.1.1**

* New dashboard view.

**2.1**

* Upgrade to have eShop import feature and advance media handling with shortcode.
* Upgrade for compatibility with 4.3.1

**2.0.7**

* Upgrade for apache related issue fix.

**2.0.6**

* Upgrade for issue fixes.

**2.0.5**

* Upgrade for issue fixes and 4.2.2 compatibility

**2.0.4**

* Upgrade for issue fixes and 4.1.1 compatibility

**2.0.2**
* Upgrade for issue fixes and 4.1 compatibility

**2.0.1**

* Upgrade for dynamic debug mode enable/disable feature.

**2.0**

* Much advanced importer with import manipulation.

**1.2.1** 

* Security Update

**1.2.0**

* Upgrade for performance improvement and more new features.
 
**1.1.0**

* Upgrade to have comment import feature.

**1.0.0**
	
* Initial release of plugin.
