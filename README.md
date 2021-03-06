<h1>DEVELOPER NOTES DRAFT</h1>
<h2>PeopleAggregator Social Networking Platform</h2>
<h5>
updated for V2 19 March 2009<br>
updated for V2.1 19 August 2009<br>
updated for V2.2 10 June 2010<br>
</h5>

THIS STILL NEEDS MORE UPDATING!

<h3>Installation Guide, and Documentation:</h3>
<li> General Info etc: The wiki on our github page

<h3>Official Release of PeopleAggregator:</h3>
<li> Tarball download: http://update.peopleaggregator.org

<h3>Unofficial Release:</h3>
<li> The downloads section at the top of our github page.

<h3>About this code repository:</h3>
This repository currently provides basic structure for the
paproject/pacore system implemented in PeopleAggregator V2.

To configure this PeopleAggregator, edit the files in 
the directory paproject/web/config/

The structure is:
 [startup files and this document]
 pacore/  	- People Aggregator Core, an external SVN repository
 paproject/	- Customizations for this Project

The pacore subdirectory contains a repository for a complete
PeopleAggregator Core system.

The paproject subdirectory alongside it contains a parallel structure, initially with few files, our defaultproject.  
If we add a file to paproject of the same name and
location of a file in pacore, that file will be used instead of the
corresponding the pacore file.  We call this "shadowing" the core file.

New and custom functionality can also be added by adding files to paproject
directories.

Point your Apache server at /web which contains one file,
dispatcher.php.

Most project configuration files can be found in paproject/web/config/
The configuration files outlined below are the basic ones for
configuring your project.

-------------

<h3>LOCAL CONFIGURATION</h3>
paproject/config/AppConfig.xml
Automatically generated in the PA install process - NOT in a repository.  
It defines elements of a specific site, including DB and other
information distinct form other instances of this project.  
Distinctions between dev, staging, and live versions of
the same project can be captured in this file.

-------------

<h3>STARTUP FILES</h3>
The following two files are typically copied from the core system:
web/dispatcher.php 	-- top level dispatcher for the system.
project_config.php	-- This top level configuration file is loaded before any other file.
It contains key default data such as:
'PA_CORE_NAME','PA_PROJECT_NAME', 'DEFAULT_INSTALL_SCRIPT', 'PA_PROJECT_ROOT_DIR', 
'PA_PROJECT_CORE_DIR', 'PA_PROJECT_PROJECT_DIR', and base shadowing
path settings.

These settings could be changed if required by a new project.
Also, project specific settings data and variables required by
the project that should not be overridden by other configuration files.

Developers ordinarily do not modify these files unless instructed by BBM.

-------------

<h3>BASIC PROJECT SETTINGS</h3>
To configure basic project settings, see:
paproject/config/AppConfig.xml 
 and other files in that directory

-------------

<h3>BASIC SHADOWING<h3>
To use a different template from the one defined in pacore/web/Themes/Beta/
 - create a file with the same name in paproject/web/Themes/Default/

To use a different template from one defined for an existing module such as in:
 pacore/web/BlockModules/MyModule/template_name.tpl.php

 - create a corresponding file defining the new template behavior in:
  paproject/web/BetaBlockModules/MyModule/center_inner_public.tpl

To create a new module for this project, simply add it
 as paproject/web/BlockModules/MyNewModule

-------------

<h3>ADDING API FUNCTIONS</h3>
To add project-specific API functions:
1) Add API extensions in a file named paproject/tools/webapiwrappergen/project_api.api.
Follow the example provided at:
 paproject/tools/webapiwrappergen/projectapiexample.api

2) Implement your API methods in project_api_impl.php and put them in paproject/web/api/lib.
See pacore/web/api/lib/api_impl.php for examples.

3) Build the API with paproject/tools/build_test_api.sh from the
command line.  This will build the API file as well as the core peepagg.api and give you
a combined descriptor in web/api/lib/api_desc.php.  

See the build example at:
 paproject/tools/build_test_api.sh

-------------
<h3>ADD PROJECT-SPECIFIC DB UPDATES</h3>

To add project-specific database updates:

Add QUP database update code to:
 paproject/web/extra/project_db_update_page.class.php -- this is run first
 paproject/web/extra/project_net_extra.class.php      -- this is run afterward
See pacore/web/extra/db_update.php for examples of QUP code.

NOTE: Now DB updates are performed from the browser, by visiting:
/update/run_scripts.phpi

------------
