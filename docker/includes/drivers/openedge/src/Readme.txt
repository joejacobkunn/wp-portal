
Progress OpenEdge 11.7.0.0 Readme

Ports: All
Date: March, 2017
--------------------------------
Copyright (c) 1984-2017 Progress Software Corporation.  All rights reserved.


Read these On-line Release Notes
================================

It is important to read these on-line release notes.  They are the exclusive release notes for this release.

The on-line release notes are on the distribution medium as one text file, readme.txt.

The readme.txt file includes a list of known issues followed by a list of the issues fixed in this release.

In addition, the distribution medium includes the following HTML report files which list the issues addressed in 
this release -  organized by version, issue number (CR ID), or product component.

  * PROGRESS_OE_<release#>_VERSION.htm (Lists fixed issues by Version)
  * PROGRESS_OE_<release#>_ISSUE.htm (Lists fixed issues by Issue Number)
  * PROGRESS_OE_<release#>_COMPONENT.htm (Lists fixed issues by Component)

---------------------------------------

PRODUCT NOTES for 11.7.0.0

a. ADE Tools and Runtime

PSC00352357 : HTTP Client Execute() throws an input-blocking error for a
pending WAIT-FOR statement
================================================================================
The HTTP Client Execute() method throws an input-blocking error when used with
the OpenEdge GUI for .NET, or potentially any interactive session that has a
pending WAIT-FOR statement. The standalone HTTP Client code executes without
error when run in the Procedure Editor or the AppBuilder. When executed from a
GUI for .NET form, the same code throws an input-blocking error (2780) on the
HTTP Client's Execute() method.


PSC00350851 : PauseBetweenRetry property delays first execution of its OpenEdge
HTTP client request
================================================================================
The PauseBetweenRetry property delays the first execution of its OpenEdge HTTP
client request. This delay matches the value set for PauseBetweenRetry. 


PSC00350767 : OpenEdge.Core.String:isQuoted() cannot handle an empty string
parameter
================================================================================
In the OpenEdge 11.6.3 service pack, the OpenEdge.Core.String class includes a
new static method IsQuoted().

Passing an empty string to this method fails with error "** Starting position
for SUBSTRING, OVERLAY, etc. must be 1 or greater. (82)".


PSC00354001 : URI encoding now uses RFC3986
================================================================================
The URL encoding code used in OpenEdge.Net.URI is taken from the classic
WebSpeed encode-url function which is based on RFC 1738
(https://tools.ietf.org/html/rfc1738). In RFC1738 the tilde forms part of the
'unsafe' character set which no longer appear in RFC3986.

From 11.7.0 onwards the URI Encode method uses the encoding described in
section 2 of RFC3986  https://tools.ietf.org/html/rfc3986#section-2 


PSC00350770 : OpenEdge.Core.String:isQuoted() cannot handle a parameter with
multi-byte characters
================================================================================
In Unicode sessions (-cpinternal UTF-8), OpenEdge.Core.String:isQuoted()
returns FALSE for a parameter passed as a properly quoted string with
multi-byte characters.


PSC00349581 : GetHeader() never returns the Unknown value (?)
================================================================================
A call to an HTTP Message's (request or response) GetHeader() method ALWAYS
returns a valid object reference, even if the message does not have the header
in question. If the header does not exist, an instance of a NullHeader is
returned, which has its Name set to the Unknown value (?) and its Value set to
the Unknown value (?).

To determine if a header exists in the message, use the HasHeader(<name>)
method.


PSC00200793 : Initial value rules for logical fields in the GUI Dictionary
================================================================================
By default, when you create a logical field through the GUI Data Dictionary, it
sets the Initial Value as the first value in the Format. For example, if the
Format is "male/female", the Initial value defaults to male. The exception to
the first item rule is when the format is "yes/no" or "true/false". For those
cases, the tool uses the OpenEdge default value for a logical field, "no" and
"false", respectively.


PSC00172720 : Mandatory fields are not marked as NOT NULL during protoodbc
================================================================================
When running a pro-to-odbc migration against a DB2 data source, fields marked
as "mandatory" in the OpenEdge database were being created in generated SQL
script and the foreign data source without the NOT NULL syntax.  This leads to
a situation where these fields are null capable in the foreign database.  

To make DB2 consistent with the MS SQL Server and Oracle DataServers, the
OpenEdge DataServer for ODBC will now set the NOT NULL constraint on SQL
columns derived from mandatory fields for DB2 data sources.


PSC00155578 : Generic comment deletion code removed from triggers
================================================================================
Deletion triggers have been updated to not generically remove comments,
security allocation and multi-media records due to performance impact. A new
ERWin trigger template db/af/erw/afercustrg.i has been provided that customers
can use to re-generate their own triggers without the generic deletion code.
Customers can also update their triggers with specific code to do the deletion
in such a specific way that should not have any performance impact.


PSC00240314 : Migrating Progress Dynamics to Release 11.0+
================================================================================
Before attempting any migration, convert the physical database to Release
11.0+, using the proutil conv1011. Progress Software Corporation recommends
making a backup at this time.

Before migrating to Release 11.0+, Progress Dynamics currently using Version
2.1B must upgraded to Version 2.1B02. The following steps are the only
supported path to migrate a Version 2.1B Repository to OpenEdge 10.2B:
- Ensure the Repository is upgraded to Version 2.1B02 (db version: 020030). The
migration does not work properly on earlier versions of the Repository.
- Create a new shortcut for the Dynamics Configuration Utility (DCU), based on
the existing shortcut from the install.
- Modify the -icfparam parameter in the target. Change
'DCUSETUPTYPE=ProgressSetup' to 'DCUSETUPTYPE=Migrate21Setup'.	
- Run the Dynamic Configuration Utility (DCU) from this shortcut to upgrade the
Repository from Version 2.1B02 to Release 11.0+.


To migrate from Release 10.0B05 to Release 11.0+, follow these steps:
- Ensure the Repository is upgraded to Release 10.0B05 (db version: 100005).
The migration does not work properly on earlier versions of the Repository.
- Create a new shortcut for the Dynamics Configuration Utility (DCU), based on
the existing shortcut from the install.
- Modify the -icfparam parameter in the target. Change
DCUSETUPTYPE=ProgressSetup to DCUSETUPTYPE=Migrate100Setup.  
- Running the DCU from this shortcut upgrades the Repository from Release
10.0B05 to Release 11.0+.


To migrate from Release 10.1A02 to Release 11.0+, follow these steps:
- Ensure the Repository is upgraded to Release 10.1A02 (db version: 101002).
The migration does not work properly on earlier versions of the Repository.
- Create a new shortcut for the Dynamics Configuration Utility (DCU), based on
the existing shortcut from the install.
- Modify the -icfparam parameter in the target. Change
DCUSETUPTYPE=ProgressSetup to DCUSETUPTYPE=Migrate101ASetup.  
- Running the DCU from this shortcut upgrades the Repository from Release
10.1A02 to Release 11.0+.


To migrate from Release 10.1B03 to Release 11.0+, follow these steps:
- Ensure the Repository is upgraded to Release 10.1B03 (db version: 101101).
The migration does not work properly on earlier versions of the Repository.
- Create a new shortcut for the Dynamics Configuration Utility (DCU), based on
the existing shortcut from the install.
- Modify the -icfparam parameter in the target. Change
DCUSETUPTYPE=ProgressSetup to DCUSETUPTYPE=Migrate101BSetup.  
- Running the DCU from this shortcut upgrades the Repository from Release
10.1B03 to Release 11.0+.

To migrate from Release 10.1C to Release 11.0+, follow these steps:
- Ensure the Repository is upgraded to Release 10.1C (db version: 101201). The
migration does not work properly on earlier versions of the Repository. The
migration will also work for service packs 1 and 2.
- Create a new shortcut for the Dynamics Configuration Utility (DCU), based on
the existing shortcut from the install.
- Modify the -icfparam parameter in the target. Change
DCUSETUPTYPE=ProgressSetup to DCUSETUPTYPE=Migrate101CSetup.  
- Running the DCU from this shortcut upgrades the Repository from Release 10.1C
to Release 11.0+.

To migrate from Release 10.2A to Release 11.0+, follow these steps:
- There were no updates made to the Repository between 10.2A and 11.0, and so
no migration is necessary.

To migrate from Release 10.2B to Release 11.0+, follow these steps:
- There were no updates made to the Repository between 10.2B and 11.0, and so
no migration is necessary.


PSC00178641 : DCU enforces MinVersion with migrations
================================================================================
If a setup XML file has migration setup types (defined by having a
migration_source_branch session property with a non-blank value), then a check
is performed to ensure that the DB version sequence matches that of the
MinimumVersion in the Database node of the setup_type. If these 2 versions
don't match an error is raised and a message shown.

This message is a new message and has a code of 'MSG_cannot_migrate', and
appears in the shipped setup101B.xml. If customers have created their own
setup.xml files, they need to add this message to all the setup types contained
in those XML files.


PSC00174297 : Dynamics translated application: performance problems on login
================================================================================
Support has been added for a session property called "cached_translations_only"
which is set via the session definition (in the XML file and/or repository). In
the absence of such a property in the session, the default is YES (the
historical value). If cached_translations_only is false, then translations are
not retrieved at startup, but rather on demand when an object is translated.


PSC00162290 : Static Object Deployment Tool - Option to generate source listing
================================================================================
The Deploy Static Objects tool has the ability to produce a listing file of all
the files that are packaged into a deployment produced by the tool. 

The listing file is specified in the UI before the deployment starts building.
If no listing file is specified, then the listing will not be produced. A
default value is provided by the tool, and is for a file called 'listing.log'
in the session's temporary directory.

The listing file consists of the following fields, tab-delimited:
File name
Relative path
Deployment type
Design-only flag

This functionality can be used to determine which files are required for
compilation on 64-bit platforms. A Dynamics client is only supported on
Windows, which is a 32-bit platform, and customers may connect to an AppServer
running on a 64-bit platform; in such a case the code would need to be compiled
against the 64-bit platform, and the listing file will give the ability to
determine which files are required.


PSC00140234 : Notes on using the Deploy Static Objects tool
================================================================================
When deploying static objects using the Deploy Static Objects tool, please note
the following:

1) You must specify the following extra directories in the Deploy Static
Objects tool, in order for a standard Progress Dynamics WebClient install to
work:
    adeicon 
    ry/img
    adm2/image	

adeicon.pl can be used instead of the adeicon directory, but you must manually
copy this file into the target directory. These image directories are not
required for a server install (AppServer of Web).

2) You must manually copy the adm2/dyntreew.wrx file into the target directory,
for WebClient installs.

3) You must manually copy adecomm.pl into the target directory for a WebClient
install.


PSC00314904 : The adm2/dyntreeview.w SmartObject only works in 32-bit installs
================================================================================
The adm2/dyntreeview SmartObject is implemented using a 32-bit OCX control. An
error message about the OCX not being registered or moved will be thrown if you
attempt to run or drop this SmartObject on a container in a 64-bit install.


PSC00240288 : The DynamicsVersion session property must be manually updated
================================================================================
If you are working in an OpenEdge release later than 10.2B, the DynamicsVersion
session property may be set to the wrong release number. This property is not
automatically updated.

To workaround this issue, modify
$DLC/src/dynamics/db/icf/dfd/update_dynamics_version_property.p so that the
value of the DYNAMICS-VERSION preprocessor is set to the relevant OpenEdge
release number (11.2, for example). Compile this file and copy to $DLC/gui,
which is important because the DCU does not run against source code.


PSC00224776 : ADM2 default data management ignores orphaned/unlinked SDOs on
Viewers
================================================================================
A SmartDataObject (SDO) placed on a Viewer without being linked to a data
source or to a field is not included in the container's first request to the
AppServer and will thus not get any data. This was not the case in old releases
of OpenEdge (before v10).  

You can uncheck the Appserver Aware option in the Procedure Settings of the
container to get the old behavior. This makes the SDOs revert to the old
request behavior with one AppServer request per SDO. Note that this can add a
substantial overhead to the data retrieval from the  AppServer. 

Alternatively, you can move the SDO to the SmartContainer and use the pass-thru
link mechanisms to make it accessible to the Viewer, or override
initializeObject in the Viewer and call openQuery in the SDO. This approach may
require one extra AppServer request specifically for the SDO. 


PSC00219897 : Dynamic SDO with join require calculated fields to be defined in
table order
================================================================================
In a Dynamic SDO based on a join, calculated fields will behave incorrectly
under these circumstances:
- Both tables in the join have calculated fields defined in the Repository's
entity definitions.
- Both tables also have one or more of these SDO Entity calculated fields
included in the SDO.
- The calculated fields from the right-hand table of the join appear before
those of the left-hand table in the list of fields in the SDO.

Symptoms include incorrect formatting and incorrect initial values being
provided, as well as calls to the SDO data logic procedure failing with errors
relating to temp-table schema mismatches.


PSC00207823 : dynlaunch.i fails in thin client with DATETIME, DATETIME-TZ, RAW
or ROWID param
================================================================================
Calling a PLIP using dynlaunch.i fails in a thin-client environment if the
procedure call includes DATETIME, DATETIME-TZ, RAW or ROWID parameters.

The client will report this error:
Mismatched number of parameters passed to routine <name>. (3234)

The AppServer agent will report these errors:
BUFFER-FIELD was not found in buffer ttSeqType. (7351)
Lead attributes in a chained-attribute expression (a:b:c) must be type HANDLE
or a user-defined type and valid (not UNKNOWN). (10068)

The workaround is to implement calls with these data types to run as external
procedure calls on the AppServer.  

e.g. RUN xxx on gshAstraAppServer (INPUT ...)

Alternatively, you might add CASE statements for the 4 data types in
obtainInitialValueField in adm2/caller.p. (This has not been tested, but is
assumed to be the problem.)


PSC00195316 : **Widget ID <n> not unique (<widget-name>) error for recursive
Dyn TreeViews
================================================================================
The "Runtime Widget-ID Assignment" tool assigns default gap values for the
imported SmartObjects.
These default values should work without problems in most cases. But, for
Dynamic TreeViews with structure nodes, the default gap value of 2000 for
"TreeNode" might not be large enough.
Since the nodes are created at runtime according to the database data, it is
impossible to know how many tree node levels are going to be created in the
Dynamic TreeView. If this happens and the default gap value of 2000 is not
enough, that default value should be increased as specified in the "Application
Development Environment (ADE) Addenda" Web paper in the "Widget IDs for ADM2
and Progress Dynamics" section.


PSC00194792 : Toolbar never creates a Band more than once per toolbar instance
================================================================================
The toolbar creates only a single instance of each Band for a toolbar instance.
However, the Repository allows multiple instances of the same Band on a
toolbar. Before Release 10.1C, the toolbar created the multiple instances of
the same Band, but the second instance had no menu items. In Release 10.1C, the
second instance of a Band is ignored completely.


PSC00180829 : DynLookup limit on number of joins may vary on DataServers
================================================================================
In Release 10.1C, the DynLookup supports a join with up to 18 tables instead of
10. But, this limit might not apply when using a DataServer, because there also
is a limit decided by the size of the ROWIDs in the tables. The ROWIDs of the
tables are added to an indexed RowIdent field and the ABL has a limit on the
size of an index. 

Starting with Release 10.1B, as long as -tmpbsize 4 (default) or larger is
used, temp-tables use large index keys and can store up to 1970 characters.
This means that, in theory, you can store and index up to 16 ROWIDs returned
from MSSQL server ( 16 * ( 116 + 1 ) - 1 = 1871 characters for rowids +
separators). The different DataServers will return ROWIDs with different
lengths, so the limit will vary. 

If this limit is an issue, then you can edit the af/app/afgettemp.p procedure
and remove the following index definition.
 
----
hTT:ADD-NEW-INDEX("idxRowIdent":U,FALSE,FALSE).
hTT:ADD-INDEX-FIELD("idxRowIdent":U,"RowIdent":U,"asc":U).
----

The index is used in certain reposition operations on the client. Removing it
has an affect on performance, but this might be unnoticeable or acceptable
since the operation is done on the temp-table on the client.


PSC00170305 : UndoChange action added to all tableio toolbars
================================================================================
Beginning with version 10.1B all shipped toolbars and tableio bands contain a
new UndoChange action. This action replaces the old Reset action as the default
in these toolbars.  

This constitutes a behavior change (label and accelerator) for existing
applications that use the shipped toolbars or bands, since the caption is
changed to "Undo" instead of "Reset" and the accelerator is changed to "CTRL-Z"
from "ALT-R". 

The new UndoChange action defines some of the translatable text needed for the
context sensitive text in the admmsgs.i include, message number 97.    

Customers that use the shipped toolbars or bands and want to remove the new
action and get the old one back need to customize the class or toolbar.
Customers that already have specific toolbars might need changes either to be
able to use the new action or to remove it.
 
To remove (hide) the new UndoChange action and get Reset back in shipped
toolbar or band:
-------------------------------------------------------------------
The Reset action is already present in the toolbar, so in order to make the
Reset into default, the UndoChange needs to be added to the comma-separated
HiddenActions property.   

Existing customer toolbars
--------------------------
Existing customer toolbars may or may not get the new UndoChange action. If the
toolbar or menu is defined with the "Tableio" category or one of the tableio
bands, then the action is added automatically. But, many toolbars add tableio
actions specifically in initAction() or initMenu() overrides in order to
control the order and add rules. The "UndoChange" will need to be added to
these.	 

customized initAction
---------------------
Since the getTableioUndoNew may need to load the actions if it is called from a
visual object before the toolbar is initialized, the toolbar initAction has
been changed to setIsActionsLoaded(true). Overrides of initAction that do not
call super must add the same call, using dynamic-function or the {set~ include.


PSC00167044 : The Export to Excel functionality removes leading zeros from
character strings
================================================================================
The Export to Excel functionality removes leading zeros from character strings.


PSC00162037 : IndexInformation property only contains information for primary
table(s)
================================================================================
The IndexInformation property has been changed to only contain index
information for the table(s) that are uniquely represented in one row in the
SDO query. The default table(s) are the EnabledTables or the first table for
read-only SDOs. 

The getIndexInfoTables function determines which tables' info is added to the
property. This is not implemented as a property, but can be overridden if
different behavior is needed. If you want the old behavior all data objects,
then add a custom version that returns the value from getTables (all tables in
the query).


PSC00161048 : Changes to how decimal values are managed in dynamic combos
================================================================================
There were several problems in previous releases developing decimal dynamic
combos with default flag values that contain a numeric decimal point and
deploying with different numeric formats.

Default flag values were always stored in the format used during development. 
This prevented deployment to numeric formats other than the one used for
development.  Either errors displayed and no data displayed in the combo or
incorrect data was stored in the repository for the <None> or <All> option.

With 2.1B01, 10.0B03 and 10.1A this has changed to store default flag values
with American numeric format.  They should be entered in the SmartDataField
maintenance tool using the session's format but the tool will replace the
numeric decimal point with a period in the stored value.  At runtime, any
stored periods in the data are replaced with the current session's numeric
decimal point.

A fix program runs as part of the DCU Upgrade to convert existing default flag
values for decimal dynamic combos to American format.  The DCU must run in the
format that was used to develop dynamic combos.

The fix program writes a message to the log file for each default flag value it
attempts to convert.  It writes a message when a value is converted
successfully or writes a message if the conversion fails.  Review the DCU log
file after the upgrade and manually correct any failures.  Any failures that
are not corrected may not behave properly at runtime as the runtime has changed
to attempt to convert stored American formats to the format of the running
session.

There are several Dynamics objects in the repository that store blank default
flag value instance attributes and give messages in the log file.  These are
for the cbSCMTool dynamic combo on the following viewer objects:

gsmsxgenviewv
gsmsxotviewv
gsmsxpmviewv

The messages for these objects are expected and you can ignore them.

Any dynamic combo instance attributes stored in static viewer code in your
application must be converted manually to American numeric format.


PSC00159054 : assignNewValue w/ invalid values behaves different in new lookup
API
================================================================================
The old and new lookup APIs behave differently when passing invalid value
parameters to assignNewValue if the lookup key field is different from the
lookup displayed field.

With the old API (pre-2.1B/10.0B02 behavior and 2.1B/10.0B02 behavior with
keep_old_api set to TRUE), passing an invalid key field value or an invalid
displayed field value to assignNewValue displays blank in the lookup field when
assignNewValue is invoked. If changes to the record are saved, blank is stored
in the record's key field.  

With the new API (2.1B/10.0B02 default behavior), passing an invalid key field
parameter displays blank in the lookup field when assignNewValue is invoked. If
changes to the record are saved, the previous value displays in the lookup
field and the value is not changed in the record's key field. Passing an
invalid displayed field parameter displays the invalid value in the lookup
field when assignNewValue is invoked. If changes to the record are saved, the
previous value displays in the lookup


PSC00158854 : Widgets on static SmartDataBrowser are not enabled on dynamic
containers
================================================================================
When a static SmartDataBrowser contains other objects on its frame, those
objects are not enabled when the static SmartDataBrowser is run on a dynamic
container.  When that static SmartDataBrowser is run on a static container,
those objects are enabled.


PSC00158601 : BaseQuery must have NO-LOCK after WHERE
================================================================================
A child SDO loses parent filter criteria if its BaseQuery has the NO-LOCK
option before the WHERE criteria.

When two SmartDataObjects are linked in a parent/child relationship, with or
without a SmartBusinessObject, the child SDO might lose its filtering by the
parent SDO. In previous versions, this happened if the child SDO was generated
by the Object Generator with Follow joins turned on. This generated a BaseQuery
with NO-LOCKs before the join criteria and caused the ADM to add the
foreignfield criteria to the query incorrectly. 

As a workaround, opening and saving the SDO in the AppBuilder alters the
BaseQuery to put the NO-LOCKs after the join criteria as expected by the ADM.


PSC00158372 : DynCombo support for a DataObject as data source does not include
static SDO
================================================================================
The option to define an SDO DataSource for a DynCombo is not supported for
static SDOs.


PSC00158340 : Sharing data object, browse and viewer out of sync showing data
================================================================================
The support for SharedData does not actively refresh viewers or browse
viewports when data is changed by another DataObject. To refresh a browse, you
must currently scroll the record out of the viewport. To refresh a viewer, you
must currently navigate to another record and back. Note that a viewer on a
hidden page does not refresh itself if it is on the same record as when it was
hidden. So, in this case, it does not help to navigate to another record and
back in the browser.


PSC00157892 : SDO Cache does not include Auditing & Comments tick-information
================================================================================
The data caching and data sharing support does not always cache auditing and
comment information. This information is retrieved only for certain instances
and would only be cached if the actual object that did the initial server
retrieval was set up to include this information. Data objects that retrieve
their data from the cache may thus be unable to show the correct tick-mark in
the toolbar if the initial server request did not include this information.


PSC00157071 : stripLookupfields is not used in the new lookup API
================================================================================
In previous versions, the stripLookupFields procedure was called on each
display to figure out if the SmartDataFields could be resolved by the viewer's
DataSource. The new lookup API does not use this procedure.


PSC00156968 : Setting of BaseQuery in initializeObject requires synchronization
of QueryWhere
================================================================================
If any other query manipulation has been done before changes to the BaseQuery
are made, you need to synchronize QueryWhere and possibly the QueryString. If
the BaseQuery is set in an initializeObject override before SUPER, you might
also need to synchronize QueryWhere in an SDO without any custom query
manipulation. The only cases where BaseQuery can be set without synchronizing
the QueryWhere are on the client (AsDivision = 'client') and in dynamic SDOs on
the server (AsDivision = 'server'). Static SDOs calls setOpenQuery, which calls
setQueryWhere, from the main block (query.i) when connected to the database.
Dynamic objects that are connected to the database will call prepareQuery from
createObjects.

Background: 
The BaseQuery is the design-time, fixed part of the query. It is part of the
object definition. The design philosophy is that runtime query manipulation can
be done without violating the integrity of this part of the expression.
Consequently, the BaseQuery should not be changed after any query manipulation
has been done.
However, it is quite common for applications to need to set this when an SDO
instance is launched. (For example, to filter data on department or company key
based on the userid.) 
Various examples and documentation have shown and stated that this can be done
in an initializeObject override before the call to SUPER. In particular, older
documentation examples showed this being done with setOpenQuery, which also
synchronizes the physical query by calling QueryWhere. This was a problem on a
client since setQueryWhere used to always prepare the physical query.
setQueryWhere has since become more lightweight, but setOpenQuery still calls
the server and should still be avoided in an AppServer session.


PSC00156174 : Translation tool does not include SmartDataBrowser search field
================================================================================
The optional search field of a SmartDataBrowser doesn't show up in the
translation window to allow you to add a translation.	    


PSC00156171 : The SmartDataBrowser sort profile is not used with SBO
data-sources
================================================================================
The sort profile saved for a SmartDataBrowser with a SmartBusinessObject as its
data-source is not used upon restart.


PSC00155989 : Changes in how Dynamics Window dimension profiles store
WINDOW-MAXIMIZED
================================================================================
Progress Dynamics windows now store the WINDOW-MAXIMIZED state together with
the previously stored dimensions in the user profile. The state is stored as a
5th entry in the profile value. In previous versions, this state just overwrote
the old profile. If you close a maximized window and no dimensions have been
saved previously, the window still stores "WINDOW-MAXIMIZED" as the only entry.


With the new behavior, a window can store the WINDOW-MAXIMIZED state without
loosing the stored dimensions for the previously saved WINDOW-NORMAL state. The
next time you launch the window, it displays maximized. But, when the
WINDOW-NORMAL state is applied, the window resumes its previously stored size.


PSC00154960 : Dynamic Lookup maintenance window does not destroy its
maintenenance SDO
================================================================================
The Dynamic Lookup maintenance window does not destroy its SDO and associated
Data Logic Procedure

Whenever you open the maintenance window of the dynamic lookup from the dynamic
lookup browse, the system launches a new instance of the maintenance window's
SDO and its associated DLP. However, these objects are not destroyed upon
closing the maintenance window.


PSC00154765 : Error messages for child SDO not returned when CLOB involved
================================================================================
A detailed error message may not be returned to the client while adding a
record to a SmartDataObject under the following circumstances:

- the SDO is running on AppServer
- the SDO contains large object fields
- AutoCommit is false (commit is used to commit multiple updates at once)

The client does receive an "update cancelled" message but should have a
detailed error.  The detailed error is written to the AppServer log file.  

This situation is more likely to occur with an SDO that is part of a
SmartBusinessObject.


PSC00154671 : SmartFrame objects stored with .w in object name may need two
translations
================================================================================
For SmartFrames or SmartWindows that are launched from a static container, the
object name for Title and Page Label translations is resolved from the
procedure:FILE-NAME without path and extension. Therefore, SmartFrame objects
that have the .w extension in their Repository object names might need two
translations, one without the extension for usage in static containers and one
with the extension for dynamic containers.


PSC00151672 : CallerProcedure, CallerObject, and CallerWindow are not set in
createObjects
================================================================================
The Session Manager's launch in Progress Dynamics sets properties like
CallerProcedure, CallerObject, and CallerWindow in the launched container.
These properties are set before the container is initialized and can thus be
used in an initializeObject override. However, they are not intended for use
during the construction (the execution of the main block) of the container. 
The createObjects call is typically done as part of the construction. However,
in previous releases, static non-window containers, like SmartFames and
SmartDataViewers, did call createObjects from initializeObject allowing these
properties to be used also in createObjects. 
Now, createObjects is called during the construction of almost all containers.
If a createObject override uses these properties, it is unlikely to work. Any
logic referring to these properties in createObjects should be moved to an
initializeObject override instead. Moving this logic to initializeObject should
not cause any change in behaviour.


PSC00150794 : Read only SDO tables can be specified to remain NO-LOCKed  during
transaction
================================================================================
The NoLockReadOnlyTables property can hold a comma-separated list of read-only
(non-updatable) tables that should remain NO-LOCKed during the transaction. A
value of 'ALL' means that all read-only tables should remain NO-LOCKed. Note
that read-only tables defined by this property also are excluded from the
optimistic lock check of changes.


PSC00144084 : Error 4054  (.. does not fit..) if ToolbarDrawDirection is
"vertical"
================================================================================
The Dynamics layout manager does not handle toolbars where the
ToolbarDrawDirection is set to 'vertical'.


PSC00140279 : Static browsers/browsers in static windows don't get Repository
column labels
================================================================================
Browsers running in static windows linked to a static SDO on an AppServer will
not get the column-label from the Repository's EntityField. Instead, the
browser gets the column-label from the database schema. This problem also
applies to any static browser.	 


PSC00140138 : A single toolbar cannot switch navigation between SDOs and SBOs
================================================================================
In a paged container where a SDO is the Data-source of a SBO, you cannot use a
single toolbar to navigate both the SDO and the SBO.


PSC00140135 : Loss of data when changing page while browse is in add
================================================================================
When adding a new record in a browser that is part of a tabbed user interface,
the entered data can be lost or appended to the next record in the browser
under the following conditions:

- The browser is the update source for an SDO. 
- The user did not enter all of the required fields in the browser row. 
- The user selects a different page before saving the new record.


PSC00133069 : ADM2 does not support the READ-ONLY attribute on browse columns
================================================================================
The ADM2 does not support setting the READ-ONLY attribute directly on browse
columns as a way to disable fields.  

Field enabling is controlled by the EnabledFields property, that is, you make
columns read-only by removing them from the enabled fields list. 

This is not considered to be a bug. Dynamic browser columns are all read-only
at start up, so it would be very difficult to implement a solution to detect if
the READ-ONLY attribute has been set in initializeObject. 


PSC00130471 : New records can be duplicated when reading new batch if added
while batching
================================================================================
If new records are created that sort higher than the last record in the current
batch, the new record is duplicated when a batch includes the new record. 

To prevent this, either ensure that the user is at the last batch when adding
the new records that will get key values that sort higher than the currently
last record or avoid batching when such records can be created.


PSC00130387 : RowObjUpd.ChangedFields is obsolete
================================================================================
Prior to Version 10.0A, the RowObjUpd.ChangedFields field was used to figure
out which fields to save in an SDO. This is now obsolete and no longer used.
The SDO now figures out which fields to save to the database tables based on a
comparison of the before-image and the changed record. The ChangedFields field
is not used in any logic, except for a call to bufferCollectChanges, which
updates this field. Since it does not capture all changes, this update is not
reliable and the method will be deprecated in the future.


PSC00129126 : Avoid changing Foreign Keys in Web Objects with joined queries
================================================================================
When you use the Detail Wizard to create a Web object that contains a join, do
not make the foreign key field updateable. If you change the value of the
foreign key in a record, the next stateless request might not be able to find
that record.

When this happens, you get an error that the query could not reposition to that
record.

If your application needs the ability to update the value of the foreign key in
a joined query, you must make sure that the list of ROWIDs in the
"CurrentRowids" attribute contains ROWIDs of the newly joined table, instead of
the originally-joined table.


PSC00128303 : Static data objects do not get initial value from Repository
================================================================================
Static SDO proxies (<sdo>_cl) do not get the INITIAL-VALUE from the
Repository's EntityField DefaultValue at run time. The temp-table's
INITIAL-VALUE attribute is a read-only attribute that is compiled into the SDO
temp-table definition from the SDO include.


PSC00124754 : Print Preview and Export to Excel do not work when the data
source is an SBO
================================================================================
Print Preview and Export to Excel options available on various browse	  
toolbars are not supported for visual objects whose data sources are	      
SmartBusinessObjects. These options will do nothing when chosen.


PSC00121785 : Record level transaction hooks must be implemented in the Data
Logic Procedure
================================================================================
You must implement a static SmartDataObject's record level transaction hooks
(create*, write*, and delete*) in the Data Logic Procedure. They will only fire
from the SDO if the SDO does not have a Data Logic Procedure.


PSC00156243 : Help cannot be mapped to static container run from the AppBuilder
================================================================================
It is not possible to map help for a static container that has been run from
the AppBuilder.  The container must be run from the Dynamic Launcher to map
help for it.


PSC00173682 : Progress Dynamics can't find the help file during AppServer
sessions
================================================================================
When the help file is set in the "security control" window, Progress Dynamics
can't find the help file using the help-menu-item in an AppServer session.


PSC00171808 : Translated labels and widgets on same row but different column
================================================================================
When translating static viewers where KeepChildPositions=Yes, translated labels
now use the existing label's width only. Also when translating static viewers,
the label's font, rather than the widget's font, is used to determine the width
of the label.

The translation of simple (LIST-ITEMS) combo-box labels is now supported for
dynamic viewers.

When translating dynamic viewers with KeepChildPositions=Yes, the label moves
to the bottom (back) so that it doesn't overlay any widgets to its left. The
label is not truncated, unless it is longer than the space available.


PSC00168877 : The DataView does not support Dynamics Comments and Auditing
================================================================================
There is no default support for Comments and Auditing when DataViews and
ProDataSets are used.

The following error is returned when attempting to save a comment for a
DataView:
									     
BUFFER-FIELD RowUserProp was not found in buffer <dataset-buffer-name>.(7351)

The Server Interface implementation could define a RowUserPropfield in the
entity temp-table to store the information needed to trigger Auto Comment and
show tick marks in the Dynamics toolbar's Auditing and Comment actions, similar
to how the SDO handles it. Using this field might make it easier to use
existing Repository Manager APIs and reuse existing SDO code. Note that the
Auditing and Comment information is stored with delimiters in the field. This
makes it difficult to use outside of the ADM and difficult to populate. The
population have to be done for each record and will likely affect performance.


PSC00155275 : Info for all users sent to client
================================================================================
When a user attempts to login in a client-AppServer configuration, the entire
list of users is sent from the AppServer back to the client. This may be an
expensive operation when there is a large number of users in the repository. To
reduce this overhead, adding the session property "abbreviatedUserTable" to all
client and AppServer session types and setting its value to "YES" will result
in only the user that is logging in having his data shipped across from the
AppServer, thereby reducing the traffic.


PSC00153575 : Static SmartDataFields (e.g. SmartSelect) cannot be translated.
================================================================================
SmartSelect objects are not presented for translation in the Translation window
at runtime.

The work around is to enter translations of SmartSelects in the Translation
Control tool. The same naming convention as for Lookups is used. You must
specify the Widget Type as 'FILL-IN' and the Widget Name as 'fiLookup' in all
cases. You must enter the Object Name as <viewer-name>:<field-name>.

If the static SmartDataViewer containing the SmartSelect is used in a static
container, you must enter the file name of the viewer, including the extension
(.w), in the Object Name. If the viewer is used in a dynamic container, you
must enter the object name of the viewer, without any extension.

This is an example based on a viewer for the Warehouse table using a
SmartSelect for the 'State' field. The file name of the viewer is
'warehousesdv.w'.

Translation for use in a static container:

Object Name: warehousesdv.w:state
Widget Type: Fill In
Widget Name: fiLookup

Translation for use in a dynamic container:

Object Name: warehousesdv:state
Widget Type: Fill In
Widget Name: fiLookup


PSC00201988 : Error: The Root node code must be specified. (AF:1) when creating
Dyn TreeViews
================================================================================
When entering data in the "Dynamic TreeView Builder" tool, you must enter the
"Object filename" value first. If you do not enter the Object filename first,
you see the "Root node code must be specified. (AF:1)" error upon saving the
data.


PSC00200646 : Error 7452 in Dynamics Dataset Export Filter
================================================================================
Customers using European numeric settings might see the 7452 error when
pressing the filter button on the Dataset Export screen.


PSC00155024 : Cascading security allocations does not work for Data Security
================================================================================
The option to cascade security allocations from a profile user does not   
work for Data security.


PSC00151136 : RTB: Problems when dyn object and super proc in different module
================================================================================
When modifying a dynamic object where the dynamic object and its super
procedure were created in different product modules, the following error may
occur:

The product module <product-module1> and the product module <product-module2>
must be the same. (AF:110)


PSC00146842 : Error 560 Generating Objects From SDO
================================================================================
Error 560 (Entry outside the range of list) is raised when the Object Generator
is used to create dynamic objects from a prebuilt SDO when the SDO has been
created by the Object Generator, and has subsequently had other tables manually
added, and the product module has sublevels in the path.


PSC00146243 : Client cache viewer tool returns errors
================================================================================
The client cache viewer tool no longer works when run against a current version
of the Repository API.


PSC00143666 : Copy-Paste in DynView is not keeping all the attributes of the
widgets
================================================================================
When copying widgets from a DynView to another DynView, some widget attribute
values are missed. For example, when a fill-in is copied, the values of the
ShowPopup and Label attributes are missed in the target DynView. This happens
for all attributes that are read from the Repository.


PSC00140156 : DataFields cut/copied then pasted are associated with RowObject
================================================================================
There is an issue when you cut or copy DataFields from one dynamic viewer and
then paste them to another dynamic viewer built from the same SDO. Until you
save and reopen the second viewer, the AppBuilder shows the new fields as being
associated with the RowObject table instead of their actual database table.


PSC00131772 : Toolbar object modifications are not updated in the Toolbar
================================================================================
011A90108
Toolbar data are cached on the client as soon as they have been read once    
from the server, so when actions, bands and/or toolbars are changed in the   
Toolbar and menu designer these changes will not always be reflected in new 
instances of toolbars.							       
       
Running the Dynamic Launcher and checking the 'Destroy ADM super-procedures' 
option will ensure that toolbar data are refreshed. The other option is to   
restart the session.						    


PSC00128502 : Fields hidden on DynView master layout in design mode if custom
layout exists
================================================================================
If a field on a master layout has its HIDDEN attribute set to TRUE, the viewer
typically displays the field in design mode. However, if a customized layout is
made for the viewer, the viewer does not display the hidden field when the
viewer reopens. This is inconsistent with the normal behavior for displaying
hidden fields in master layouts. If you need to modify a hidden field in this
situation, use the ROM tool. Do not modify the hidden field by using the 'List
Objects' button to bring up the property sheet. Doing so removes the field
instance.


PSC00125407 : scm_checks_on in Security Control is not supported
================================================================================
In Security Control, there is a toggle that switches on and off checks for the
use of an SCM tool. The use of this setting is not yet implemented in all the
places where it is relevant to check for this setting. SCM functionality is
always enabled when the SCM tool (Roundtable) is in use with Progress Dynamics.


PSC00247292 : Creating a .zip file on removable media from Visual Translator
causes errors
================================================================================
Creating .zip file on removable media from Visual Translator causes the
following errors:

Error occurred while accessing component property/method: MultiVolumeControl.
Out of present range.

Error code: 0x8002000a Zip adetran/common/_zipmgr.w (5890) Zip: Couldn't open
to write. (15).

Note:  Creating a .zip file on non-removable storage functions properly.


PSC00150235 : Changing the TranMan Language combo may cause error (132)
================================================================================
Changing the selected language on the TranMan Data Tab combo does not display
the correct translations after using the 'View', 'Sort' option from the pull
down Menu.

Double clicking on some rows might give the following error:
** This Translation already exists with Sequence number <number> Instance
Number <number> Language  Name "<language>". (132)


PSC00181371 : Two undo/reset buttons on the Standardtoolbar
================================================================================
The new UndoChange toolbar action is not supported in Dynamics Web.

By default, UndoChange replaces the Reset action in shipped toolbars and
Tableio bands. When these toobars and bands are realized in the Web, the Reset
button and menu item remain and function correctly. However, the UndoChange
action may also be realized at runtime as a non-functioning button and menu
item, labeled "Undo 1 (CTRL-Z)".

In order to suppress the UndoChange button and menu item, the UndoChange action
can be set to Hidden, or added to the HiddenActions property, for the toolbar.
For additional information, please see the note for the new UndoChange action
under GUIRENDR / SmartToolbar in this document.


PSC00144996 : The compileAll option in WebTools has a limitation of URL length
of 4096 chars
================================================================================
The compileAll option on AppManager in WebTools has a limitation of URL length
being more than 4096 chars, depending of what web browser is being used. Try to
limit the size of the compiles, number of files if you run into this problem.


PSC00139229 : HTML after WSTag not sent if WSTag runs another WebSpeed object
================================================================================
If a SpeedScript file contains a WSTag tag that runs another WebSpeed
object, the remaining HTML after the WSTag tag is not sent to the Web
browser.


PSC00130586 : WebSpeed detail program called from a WebSpeed report program
hangs
================================================================================
A WebSpeed detail program called from a WebSpeed report program might hang when
the Add button is used a second time or the Delete button is used. If the
detail program is used separately from the report program, the problem does not
occur.



b. Adapter for SonicESB

PSC00240333 : Connnection string limitation for session-managed native ESB
Adapter
================================================================================
The combined length of the connection string (composed of  a user-id, password,
and information fields) must not exceed 30,000 non-Unicode characters when
connecting to an AppServer. Exceeding the string length causes an AppServer
exception.


PSC00220618 : Agents remain locked when ESB Adapter returns a Fault in Sonic
Workbench
================================================================================
When using the OpenEdge Adapter for Sonic ESB with a session-free AppServer and
persistent procedures, it is important that you release the persistent
procedure when your ESB process completes, to prevent having a locked agent. 

When the adapter returns a fault, you must ensure the Release operation is done
in your Fault process. This requires you to keep track of your process ID and
have access to it in your Fault process.

In the Progress Developer Studio for OpenEdge/Sonic Workbench development
environment, Sonic intercepts all fault returns so that it can display the
result.  Consequently, Fault processes are never run. Setting the Command
Parameter ESB Fault Return Mode to "Continue Process with Fault"  discards the
original message where your Procedure ID is stored, so this is not a viable
solution.

To free up locked agents on the AppServer, you must restart your development
container.

Using Persistent Procedures within Sonic ESB processes is discouraged because
it greatly complicates the handling of Faults and makes your session-free
AppServer operate no differently than a session-managed AppServer.



c. AppServer

PSC00346397 : PKCS8 server certificate generated using the pkiutil utility not
supported
================================================================================
If you generate PKCS8 server certificate using the pkiutil utility, you cannot
start the AppServer. The version of RSA SSL library used in AppServer broker
does not support PKCS8 server certificate format for SSL or TLS communication.
The only supported server certificate format is PKCS5.


PSC00229780 : Startup time of AppServer broker increases when SSL is enabled
(Linux and UNIX)
================================================================================
After upgrading to OpenEdge Appserver V11, the AppServer broker's startup time
might increase if SSL is enabled.  This happens if /dev/random is not populated
with random data.  The SSL libraries used by the AppServer broker require
several kilobytes of random data to properly secure SSL connections to the
broker. 

To resolve the issue, ensure that /dev/random is populated with enough random
data.  There are two possible solutions:

- Move /dev/random to /dev/random_bak, and then add a simlink from /dev/random
to /dev/urandom.  This makes /dev/random reference /dev/urandom.  Note that
/dev/urandom is less secure.

-  Install and run rngd, the random number generator daemon.  It increases the
entropy pool used /dev/random to provide random data.


PSC00244975 : Support for Actional is removed
================================================================================
Support for Progress Actional (including all Actional interceptors for
OpenEdge), which was first introduced in OpenEdge 10.2B, is removed from
OpenEdge Release 11.1.



d. DATASERVER

PSC00242574 : Always run r-code against the schema image of the original
database
================================================================================
Prior to OpenEdge 11.3, any change to the record structure or to
compile-dependent characteristics of a column would create a schema mismatch
between compiled r-code and the schema holder database representing a foreign
schema.

Starting in OpenEdge 11.3, the Oracle and MS SQL Server DataServers introduce
"position-independent r-code" which is r-code whose schema references are
"independent" of the physical positioning of a column in the foreign data
source.  In OpenEdge 11.3, position-independent r-code means that column
positions on the server table can be rearranged with only the need to perform a
schema pull but not the need to recompile ABL code.  In OpenEdge 11.3,
introducing a new column or deleting an existing column from an existing record
structure still requires the r-code to be recompiled against the new schema
layout.

With "position-independent r-code, the r-code is dependent only on a logical
identification of the column, and not the physical position of the column. The
logical identification of a column takes place in the OpenEdge database before
migrating to a foreign data source and that logical identification never
changes once the column is defined in the OpenEdge table. Therefore, if the
columns on the server are simply rearranged, the code need not be recompiled. 
But, changes to the foreign schema do need to be pulled back into the schema
image to ensure that the new physical position of a column is re-matched
against the proper logical identifiers known to the r-code.  To avoid run-time
data corruption, never run r-code or load data (.d file) while connected
through a schema image that does not reflect the actual schema on the server.

Starting in OpenEdge 11.4, the Oracle and MS SQL Server DataServers can also
accept certain changes to column attributes that would have previously caused a
schema mismatch.  The only schema attributes that cannot change are the data
type, the number of extents in an extent column and the column name.  Now, like
the online schema capability of the OpenEdge database, these are the only
attributes that require a recompile.  Again however, any changes to the server
schema still must be pulled back into the schema image to ensure the proper
attributes are known to the run-time code.  

Starting in OpenEdge 11.4, the Oracle MS SQL Server DataServers can also allow
new columns to be added to a server record's column layout that would have
previously caused a schema mismatch.  While adding a new field will no longer
require a recompile, it is still important to always perform a schema pull so
that the schema image accurately reflects the schema structure on the server. 
If the added server column is not reflected in the schema image, then future
references to that field in DataServer applications will not recognize the
field reference on any subsequent re-compiles.	


PSC00215517 : Techniques to load .d file when table has non-updatable column on
MSSQLServer
================================================================================
In MSS DataServers, there is a restriction to a load operation from a .d file
when the table has non-updatable columns (i.e. columns on MS SQL server are of
type 'identity' or 'timestamp' or 'rowguid').
You can work-around this restriction by following one of the two options
described below.

Option 1:-
Use dump/load tools of the foreign data source instead of data from a .d file
to load your foreign table.

Note: This is the only way to retain the original values from non-updatable
columns.

Option 2:-
If the user wants newly generated values for non-updatable columns stored along
side updatable fields from the given '.d' file, write an ABL program to read
data from the .d file into a temp table and then skip non-updatable fields
while writing the temp table records to the foreign table.

Sample ABL code provided below to perform this task.

Consider a table named "test" on SQL Server side with the structure as,

TABLE [test](
[fld1] [int] IDENTITY(1,1) NOT NULL,
[fld2] [varchar](50) NULL,
[fld3] [varchar](50) NULL
)

****** sample ABL code ******

/* define temp table with the name Temptbl for table test */
define temp-table Temptbl like test.

/* pouplate temp-table Temptbl from the records in the .d file name test.d */
INPUT FROM test.d.
REPEAT:
CREATE Temptbl.
IMPORT Temptbl.
/* create a record in the foreign DB */
CREATE test.
BUFFER-COPY Temptbl EXCEPT fld1 TO test.
END.
INPUT CLOSE.

*****************************


PSC00351967 : ABL Query with datetime filter returns no data with MSSQL Server
2016
================================================================================
ABL queries with datetime filter criteria in the WHERE clause do not return the
record with MSSQL Server 2016. 
Example: For ABL "FIND customer WHERE start-date = 2004-11-02T19:44:25.007.",
where start-date datatype is DATETIME in the schema holder, MSS DataServer
generates a SQL query "SELECT cust_num, start_date FROM us.us.customer WHERE
((start_date = ? )) ORDER BY start_date DESC, cust_num"; This generated SQL
fails to fetch the record when run against MSSQL Server 2016. This issue has
been found while certification of SQL Server 2016 with MSS DataServer and can
be reproduced outside of DataServer environment.


PSC00246724 : R-code compiled in OpenEdge 11 before and after this fix must be
recompiled
================================================================================
32-bit and 64-bit datasever r-code compiled in OpenEdge 11.2.0 is incompatible
with OE 11 releases before and after it due to a change of data stored in
r-code.  Corrections are required for all r-code compiled against the
DataServer for MS SQL Server in releases other than 11.2.0.  Release 11.2.0
r-code is only compatible with its own run-time.  Moving Release 11.2.0 MSS
DataServer r-code to later releases (11.2.1+ or 11.3+) requires a recompile
even if you recompiled once already in moving to 11.2.0.


PSC00246559 : Recompile r-code in 11.2.1/11.3.0 from OpenEdge 11.2 or previous
V11 versions
================================================================================
32-bit and 64-bit platform r-code compiled on OpenEdge 11.2 (or previous
versions) must be recompiled at least once before being executed on OpenEdge
11.2.1 or later versions.  Corrections were required for all r-code compiled
against the DataServer for MS SQL Server due to 64-bit alignment issues
associated with stored r-code that affect run-time capabilities on both 32 and
64 bit platforms.


PSC00242586 : Always run r-code against the schema image of the original
database
================================================================================
Prior to OpenEdge 11.3, any change to the record structure or to
compile-dependent characteristics of a column would create a schema mismatch
between compiled r-code and the schema holder database representing a foreign
schema.

Starting in OpenEdge 11.3, the Oracle and MS SQL Server DataServers introduce
"position-independent r-code" which is r-code whose schema references are
"independent" of the physical positioning of a column in the foreign data
source.  In OpenEdge 11.3, position-independent r-code means that column
positions on the server table can be rearranged with only the need to perform a
schema pull but not the need to recompile ABL code.  In OpenEdge 11.3,
introducing a new column or deleting an existing column from an existing record
structure still requires the r-code to be recompiled against the new schema
layout.

With "position-independent r-code, the r-code is dependent only on a logical
identification of the column, and not the physical position of the column. The
logical identification of a column takes place in the OpenEdge database before
migrating to a foreign data source and that logical identification never
changes once the column is defined in the OpenEdge table. Therefore, if the
columns on the server are simply rearranged, the code need not be recompiled. 
But, changes to the foreign schema do need to be pulled back into the schema
image to ensure that the new physical position of a column is re-matched
against the proper logical identifiers known to the r-code.  To avoid run-time
data corruption, never run r-code or load data (.d file) while connected
through a schema image that does not reflect the actual schema on the server.

Starting in OpenEdge 11.4, the Oracle and MS SQL Server DataServers can also
accept certain changes to column attributes that would have previously caused a
schema mismatch.  The only schema attributes that cannot change are the data
type, the number of extents in an extent column and the column name.  Now, like
the online schema capability of the OpenEdge database, these are the only
attributes that require a recompile.  Again however, any changes to the server
schema still must be pulled back into the schema image to ensure the proper
attributes are known to the run-time code.  

Starting in OpenEdge 11.4, the Oracle MS SQL Server DataServers can also allow
new columns to be added to a server record's column layout that would have
previously caused a schema mismatch.  While adding a new field will no longer
require a recompile, it is still important to always perform a schema pull so
that the schema image accurately reflects the schema structure on the server. 
If the added server column is not reflected in the schema image, then future
references to that field in DataServer applications will not recognize the
field reference on any subsequent re-compiles.	


PSC00223191 : ZPRGRS_RECID_BUF_SIZE Option minimum value changed from 44 to 52
================================================================================
The ZPRGRS_RECID_BUF_SIZE option allows the RECID buffer size to be configured
to a customized size for the DataServer session.  This is a legacy option
occasionally used by ODBC DataServer customers and MS SQL Server DataServer
customers up until OpenEdge 11.6 where it essentially becomes obsolete.  Prior
to 11.6, the option allowed for an expansion of the ROWID format size for
customers who chose their ROWID values from large natural keys.  It was also
used occasionally by customers who used PROGRESS_RECID surrogate key to
represent ROWID throughout their database structure.  In that case, the ROWID
buffer size could be minimized to 52 after 10.2B01 and 44 prior to 10.2B01.    

The minimum RECID buffer size was changed to 52 (from 44) beginning OpenEdge
10.2B01 release up until the OpenEdge 11.5.1 release  . In OpenEdge 11.6
release, the minimum value of RECID buffer size has been modified from 52 to 15
as part of the RECID area restructuring that introduced a new ROWID format
(PRGRS_ROWID_VER,1).  While ZPRGRS_RECID_BUF_SIZE was originally made available
to expand the default ROWID format size to something that would fit the largest
natural key you had in your database (if that key was greater than the
ZPRGRS_RECID_BUF_SIZE default).  But it can also be used to keep RECID buffer
size as small as possible in the record buffer (assuming you are using small
keys like the PROGRESS_RECID index that can be generated by a DataServer
migration.  The minimum limit of 52 (which is the size of the PROGRESS_RECID
surrogate key format) would still hold true in OpenEdge 11.6 and greater if you
were to revert back to the old ROWID format using the -Dsrv PRGRS_ROWID_VER,0
connection switch.  NOTE: The "old" (PRGRS_ROWID_VER,0) ROWID format is the
format of the ROWID before version 11.6.  

NOTE: DO NOT INCREASE THIS VALUE IF THERE IS NO REQUIREMENT FOR IT.  INCREASED
BUFFER SIZES CAN HAVE A NEGATIVE EFFECT ON NETWORK PERFORMANCE RUNNING
DATASERVER'S CLIENT/SERVER.

This syntax is for setting the buffer size for the RECID area,

-Dsrv ZPRGRS_RECID_BUF_SIZE,nnnn
    (where nnnn is the new size of the area in bytes. The range limits for nnnn
are   inclusive of values between 52 to 1024.)

Note: Applications that have stored ROWIDs on permanent media prior to 11.5.0
(using PRGRS_ROWID_VER,0) will not be able to use those stored references with
an 11.6 DataServer application because 11.6 uses a new ROWID format
(PRGRS_ROWID_VER,1). 

Starting with OpenEdge 11.5.0, the in-memory format was modified to consolidate
ROWID information to reduce the network bandwidth for client/server
configurations. However, If you write ROWID's out to permanent storage media
during your 11.5.0 run-time session and then try to read records back using
your stored ROWID's in a non-11.5.0 run-time, a new ROWID format implemented in
the 11.5.0 release is rendered incompatible with all releases before it or
after it. 

On the other way, If your application stores ROWID's on permanent media in a
non-11.5.0 run-time (such as 11.5.1) but tries to locate those records using an
OE 11.5.0 run-time, they would not be identified. This is because of the
ROWID's of the 11.5.0 run-time are not formatted the same as other releases,
the incompatibility will prevent you from finding records with ROWIDs stored
permanently in one run-time format and retrieved for record lookup in the
format of a different run-time.


PSC00218590 : SQLError not getting message with SNAC-10 driver.
================================================================================
New trigger solution (CR#OE00178470) generates 'PSC-init' and 'PSC-end'
messages on trigger execution. Modified DataServer code gets these messages
using SQLFetch()- SQLError - api calls for 2 times. Observed that SNAC-10
driver is not returning the PSC-end message as part of the 2nd time SQLFetch()
with SQLError()- API calls.

This is a 3rd party SNAC-10 driver issue. Confirmed the same by using a sample
ODBC program.

Note: This behavior has NO impact on DataServer functionality. 


PSC00345721 : OUTER-JOIN fetches wrong record with Oracle12c
================================================================================
An OUTER-JOIN query with 3 levels of JOIN returns only matching records from
the parent table. All parent table records that have no matching records in
child buffer are not returned when there is an external/unknown field
reference. This issue is observed with the Oracle 12c version 12.1.0.1.0 and
not in the Oracle 12c release 12.1.0.2.0 or with the Oracle 11g release.á

The incorrect behavior might appear as shown in the following query:

DEFINE QUERY qr FOR Customer, Order, OrderLine.

Open Query qr FOR EACH Customer,
á á á á á á á á á EACH Order OF Customer WHERE Order.OrderNum = 10,
á á á á á á á á á EACH OrderLine OF Order WHERE Order.OrderNum = 10.

REPEAT:
á á á á GET NEXT qr.
á á á á IF NOT AVAILABLE Customer THEN LEAVE.
á á á á DISP Customer.CustNum Order.OrderNum OrderLine.ItemNum.
END.


PSC00352694 : ODBC DataServer retired and conditional backward compatibility
provided
================================================================================
OpenEdge ODBC DataServer is now retired. When you upgrade to OE 11.7, your OE
11.7 DataServer client cannot connect to the ODBC Datasource using an ODBC
DataServer in self-service configuration. However, the ODBC DataServer will be
able to connect to an ODBC DataSource of a supported release of ODBC
DataServers only in Progress networking mode to provide backward compatibility.


Note: An OpenEdge 11.7 Dataserver client can connect to an OpenEdge 11.6 ODBC
Dataserver as far back as the OpenEdge 10.2B08 Dataserver. 

Any attempt to make a connection to an ODBC datasource using an 11.7 ODBC
DataServer will result into the following error message:

In self-service mode:
---------------------------------
" License of ODBC DataServer cannot be obtained due to product retirement" and

In progress networking mode:
---------------------------------
"Disconnected from server because database name was incorrect. (437)"
"Failed to connect to the ODBC database. (6142)" 

However, you can still connect to an ODBC DataSource using a supported ODBC
DataServers older than 11.7 in Progress networking mode.



e. DB

PSC00353684 : CDC split records with multi-byte codepages
================================================================================
If your CDC-enabled database has a double-byte or UTF-8 codepage, and you have
split change table records, the records might not split on a character
boundary. Therefore, the ABL client that tries to rejoin the change table
records must run with -cpinternal of the same codepage as the database. This is
not an issue for SQL clients accessing the change table data.


PSC00352111 : MVSCH in 11.6 can corrupt 'special' schema indexes
================================================================================
Using the move schema utility (PROUTIL MVSCH) in 11.6 can corrupt 'special'
schema indexes.

'special' schema is present when any of the following features are enabled:
- Auditing
- Key events
- Transparent Data Encryption

The problem was introduced in Release 11.6.0, and is present in the 11.6.x
service packs.
It is fixed in 11.7.0 and by hotfix 11.6.3.011.


PSC00349507 : PROUTIL CONV1011 of databases with greater than 65536 user
records have some corrupt _user records
================================================================================
If you created your 11.x database by running PROUTIL CONV1011 on a Release 10
database, and your _user table contains greater than 65536 users, then you have
user records missing two fields that were added in Release 11.	This problem
will remain latent until you attempt to use those fields.

CONV1011 is fixed in 11.6.3. 

This problem only exists in databases converted with CONV1011 from Releases
11.0.0 - 11.6.2, when the database contains greater than 65536 users.

You can determine if you have such records by using DBTOOL option 3 (Record
Validation).
Problem records will report the following error:
     Number of fields mismatch.  Expected 32, got 29.
     There were 1 error(s) found in 188861 of _User(-5).

Correct this problem by running DBTOOL in Release 11.6.3.  In DBTOOL, the
records are repaired by choosing 12 (Schema Fixup), and then 2 (_User Records
Missing Fields Fixup). After the repair with DBTOOL, run PROUTIL IDXBUILD on
the _user table.


PSC00347300 : Index for the _user table may be corrupted in databases upgraded
with PROUTIL CONV1011
================================================================================
The indexes for the _user table can be corrupted in databases upgraded with
PROUTIL CONV1011.

If an index is corrupt, you will see user login processes with the error (710):
** Your Password and Userid xxxxx do not match. (710)

The cause of this error is internal structures (holders) for deleted index keys
in Release 10 pass through the CONV1011 remaining in Release 10 format, instead
of being resolved for Release 11.  They then no longer compare correctly in
Release 11 and interfere with the user login process.

This issue is fixed in CONV1011 in OpenEdge 11.6.3 and later.

For databases that have already been upgraded from Release 10 to Release 11 via
CONV1011, you can determine if you have this issue by running PROUTIL IDXCHECK
on the indexes of the _user table.  If you have this issue, IDXCHECK will
report: found duplicate delete holder entry.

Repair the problem by rebuilding the indexes of the _user table with PROUTIL
IDXBUILD.  IDXBUIILD will not report duplicate delete holder entries during the
index build.


PSC00220715 : dbutil.bat supports nine command line parameters
================================================================================
dbutil.bat supports 9 command line parameters.	Use proutil.bat if your command
line requires more than 9 parameters.  proutil.bat supports 27 parameters.  


PSC00220401 : An encryption-enabled, manual start db cannot be started by
failover clusters
================================================================================
You cannot start a database enabled for failover clusters with your operating
system cluster resource manager if it is also enabled for transparent data
encryption and configured for manual start.

Failover cluster and encryption-enabled databases must be configured for
autostart to be started by the cluster resource manager.

If you attempt to start an encryption-enabled database configured for manual
start with the operating system cluster resource manager, the actual startup
fails, 
but the cluster resource manager may incorrectly report that the database has
been started.  


PSC00226402 : Promon output format is changed to include additional fields
================================================================================
In Release 11.0.0, new columns are inserted into promon reports to provide
information on new fields, for example domains, tenants, and partitions.  Refer
to "OpenEdge Data Management:  Database Administration" for more details. 
Understanding the change in column location is particularly important if you
have scripts that parse promon output and act on the parsed results.


PSC00354680 : Keystore file lock error returned by PROUTIL EPOLICY
================================================================================
A keystore file locking error may occur when running a large number of
concurrent PROUTIL EPOLICY commands.  The error is:

     dbEncKeystoreAccess: Internal security service error {8}  (keystore)
keystore lock file open failure {02021f0e} (15014)

This error causes the PROUTIL EPOLICY command to not execute because it could
not gain access to the TDE-encrypted database's keystore.   

Restart the failing PROUTIL EPOLICY command if this error is encountered.


PSC00256028 : Database log messages contain GMT timestamps
================================================================================
During regular execution, when OpenEdge processes write to the database log
file (.lg) the timestamp header of the message shows the time in local time
format. This header with local time information includes the offset of the
local time from Greenwich Mean Time (GMT).

For example: "[2013/04/25@11:00:34.123-0400]" which shows that the time is 4
hours offset from GMT.

On UNIX systems, when OpenEdge processes field exceptions (either internal
fatal errors or signals from other processes), messages may be written to the
database log (.lg) file from within the process' Signal Handler code. These
messages, when written from the Signal Handler, show a timestamp header in
Greenwich Mean Time (GMT) format.

For example: "[2013/04/25@15:01:07.000+0000]" which shows that the time is
actual GMT with 0 hours offset.

In addition, the time information written from the process' Signal Handler does
not show millisecond values.

This change in timestamp formatting allows OpenEdge to avoid calling certain
UNIX system functions which can cause a process to hang or not properly
terminate if called from within the Signal Handler code.



f. Doc

PSC00240336 : Problems with context-sensitve help in Progress Developer Studio
for OpenEdge
================================================================================
When launching context-sensitive help in Eclipse, Help Not Found errors
sometime occur even when help is available. The error message is, "The context
help for this user interface element could not be found."
 
If you get a Help Not Found error after pressing F1, or clicking the help icon
in the UI, try one of the following:

1. Click on some other element in the UI and press F1, or click the help icon
again.

2. Click on "Search for . . ." link in the help pane. A list of relevant topics
appears.


PSC00232614 : Incorrect file location reference
================================================================================
In OpenEdge Development: Progress Dynamics Administration, there are incorrect
references to the location of the intmplframe.w file.

The file can be found at  src/dynamics/install/obj/intmplframe.w.


PSC00183192 : Search of PDF files for UNIX Platforms
================================================================================
On UNIX platforms, the search index is not automatically attached to the
documentation PDF files.  To attach the index file so that you will have search
capabilities, follow these steps:
 1. From the Adobe Acrobat Reader, click on the Search icon.  The Adobe Acrobat
Search dialog box appears.
 2. In the Adobe Acrobat Search dialog box, click on the Index button and
choose Add.
 3. From the drop down list of files, select the oeidx.pdx file and choose OK.


PSC00183191 : Non-existing examples mentioned in online help for CSSPIN
================================================================================
The CSSpin Active X Control Help file mentions the following example:
   Example Location 
   Project File Form/Basic File
   SPINDEMO.VBP SPINDEMO.FRM
These example files do not exist.


PSC00183183 : Microsoft Security Update may affect remote access of Online Help
================================================================================
If you receive the following error when accessing the help remotely: 
"Action cancelled Internet Explorer was unable to link the web page you
requested. The page might be temporarily unavailable", see the Microsoft
Knowledge Base article titled: "Certain Web sites and HTML Help features may
not work after you install security update 896358 or security update 890175"
located at http://support.microsoft.com/kb/892675/. The article describes the
reasons for the issue and provides workarounds.



g. GUI

PSC00238681 : VideoSoft IndexTab control does not display properly on certain
platforms
================================================================================
The VideoSoft vsIndexTab OCX does not display at runtime on certain platforms
(for example, Windows 7 64-bit).  This is a third-party issue, and occurs
whether developing in Visual Studio or with OpenEdge.


PSC00237832 : Crystal Reports XI causes crash in some environments
================================================================================
Adding an instance of the Crystal Reports XI ActiveX Report Viewer to a window
causes a crash on certain platforms (such as Windows 7 or Windows 2008 Server)
and with certain Report Viewer versions (namely V11.5 or later 11.X versions). 
This is a third-party issue, and occurs whether developing in Visual Studio or
with OpenEdge.


PSC00258689 : AppBuilder in Developer Studio does not support SHIFT in
accelerators
================================================================================
The ABL does not support SHIFT as second modifier for modified single key
accelerators. Developer Studio bindings defined with ALT-SHIFT-X or
CTRL-SHIFT-X (The "x" refers to any printable char.) will thus not work when
the focus is in the design window. Progress will send CTRL-X or ALT-X to
Eclipse ignoring the SHIFT and fire corresponding bindings if defined. This
means that the ABL UI Designer do not respond to CTRL-SHIFT-W  (Close all) and
CTRL-SHIFT-S (save all) in the Developer Studio default Theme. 



h. Install

PSC00355468 : File already exists error
================================================================================
If you do not have the Application Experience Service enabled and running on a
Windows virtual machines, you get an error 'Cannot create a file when that file
already exists.' Enable and restart the Application Experience Service using
the Run ->Services. 


PSC00339996 : Ambiguous Shortcuts on Windows 10
================================================================================
When multiple versions of OpenEdge are installed onto the same computer running
Windows 10 or Server 2012 R2, the start/metro screen shortcuts for OpenEdge for
both versions are merged together by the Windows Operating system.

The following Powershell workaround can be used to resolve this shortcut issue
where multiple verisons of OpenEdge installed on one of these systems.	It will
rename the files in the start menu/metro screen:

$ProgressStartMenuDir="C:\ProgramData\Microsoft\windows\Start
Menu\Programs\progress"
$OEreleases=(get-childitem $ProgressStartMenuDir |  where {$_.Attributes -eq
'Directory'}).name
foreach  ($Eachrelease in $OEreleases)
{ 
    $Version=$Eachrelease.replace('OpenEdge','')
     #write-host  $Version
     cd  $ProgressStartMenuDir/$Eachrelease	    
    $files=get-childitem *.lnk -rec | where {$_.Name -Notlike "*$Version*"} 
    foreach ($file in $files)  
    {  
	       $newfileName = $file.Name.Replace(".lnk", "$Version.lnk")   
	       rename-item $file -newname $newfileName; 
	       #write-host  $file
    } 
	       
}


Instructions to run the code above:
1) Create a text file that contains the code above, and name the file with the
.PS1 file extension
2) Run the newly created  .PS1 script with Administrator privileges

Important Note: Since the workaround renames existing files to include verison
information, the standard OpenEdge uninstall will not remove these shortcuts. 
On an uninstall after applying the work around, the shortcuts must be deleted
manually from ôC:\ProgramData\Microsoft\windows\Start
Menu\Programs\progress\<Uninstalled _OE_Release>".


PSC00200154 : Sonic container startup shortcut requires quotes for directories
with spaces
================================================================================
If you install OpenEdge into a directory with a space in the name (e.g.
C:\Program Files\OpenEdge), the shortcut to start up the sonic container does
not function properly.	You can fix the problem by editing the shortcut and
enclosing the Target: and Start in: fields in double quotes.


PSC00351182 : Issue after uninstalling OpenEdge 11.6.x from a machine that has
11.7 also installed
================================================================================
If you have simultaneously installed OpenEdge 11.6.x and 11.7 on the same
machine, and if you uninstall OpenEdge 11.6.x then the uninstall of OpenEdge
11.7 fails or you get the following error when launching Progress Developer
Studio for OpenEdge 11.7: 
"Unable to locate installation information for OpenEdge version: 11.7". 

This is because the PROGRESS registry key in the PSC section in the HKLM and
HKCU registry hives is getting deleted during the uninstall of 11.6.x.

Workaround:

As a work around, you must recreate the PROGRESS registry key for OpenEdge
11.7. Either manually create the PROGRESS registry key and its value in the
required hierarchy or export the registry key from another machine that has the
same version installed and import to the current machine. 


PSC00326579 : Installscript set-up launcher unicode stopped suddenly
================================================================================
OpenEdge 11.5.1 fails to install and displays an error message, "Install script
setup launcher Unicode has stopped working" when you try to install OpenEdge
upon FCS. However, it succeeds sometimes. 
If issue persist, add the following registry key (case sensitive):
HKEY_CURRENT_USER\ISlogit


PSC00196833 : .Net Framework not installed with unknown default language
================================================================================
Progress Developer Studio and OpenEdge Advanced UI Controls have a dependency
on the Microsoft .NET Framework v4.0.

The OpenEdge installation media includes the English version of the Microsoft
.NET Framework, and the OpenEdge installation process will automatically
install the framework if it is not already your system, provided that your
system's locale is set to English.  

If the locale on your system is set to something other than English and the
.NET Framework 4.0 for your locale is not already installed, then Progress
Developer Studio and OpenEdge Advanced UI Controls do not install properly. 
OpenEdge will not install the English version of the .NET Framework when the
system locale is not English, and the Advanced UI controls will not install
without the .NET Framework installed.

To work around this problem, install the Microsoft .NET Framework v4.0 for your
locale, prior to installing OpenEdge.  

To download different languages of the .NET Framework 4.0 as well as the
Windows 64-bit version:
  - Login to http://www.progress.com/esd
  - Choose "Progress OpenEdge Deployment Components"
  - Choose "Microsoft .NET Framework" download page


PSC00196736 : Unknown Publisher message with a Netsetup Install
================================================================================
After performing a Netsetup install on Windows the user may see an Unknown
Publisher message when running any OpenEdge .EXE files from a network drive. To
resolve this issue, add the network drive share to the client's trusted zone.

To add the network drive to your trusted zone:

1. Open the Internet Options Dialog Box from an Internet Explorer session.
2. Select the Security Tab.
3. Select the Local Intranet icon from the zones shown.
4. Click the Sites command button, which opens a Local Intranet Dialog Box.
5. Click the Advanced command button, which opens a dialog box where you 
add and remove websites from your intranet.
6. Add \\servername\ to the list of websites. Click Close, then OK to return to
the main Internet Options Dialog Box.

You may have to reboot the client for this to take effect.


PSC00356052 : Backup of configuration at uninstall time could result in hang of
uninstall process.
================================================================================
At uninstall time the user is asked whether a configuration backup should be
performed.  If the only OpenEdge products installed on the computer are ones
which have no configuration to back up such as BPM Modeler the uninstall could
hang.  The work around for this issue is to terminate the uninstall process and
rerun it, answering "No" to the question about performing the configuration
backup.


PSC00216026 : The WebClient One-Click install fails as a Non-Admin User
================================================================================
The WebClient One-Click install (OCI) (over-the-web install) fails when run by
a non-admin on a system that does not already have the Installshield Setup
Player installed as an Add-On.	For this to work, the add-on must already be
installed/registered by an Administrator. Running the WebClient One-Click
install as Administrator just one time will get the Setup Player installed.
After this is done the WebClient One-Click install can be run as a
Non-Administrator. This issue only occurs when installing Over-the-Web. This
issue does not occur when running the WebClient installation directly via the
setup.exe file.


PSC00299643 : Personal install of WebClient fails with Internet Explorer
================================================================================
When you do a web-based install of WebClient using Internet Explorer, you get
the following error message:  

---------------------------
Feature transfer error
---------------------------
Feature:	Disk<1>
Component:	<Disk1>Disk1 Files(1)
File:		C:\Users\User.W764\AppData\Roaming\InstallShield Installation
Information\{59728F20-15EE-4C15-BB4F-780CDF156ADA}\
Error:		Access is denied.

This is a third party limitation. The only workaround is to use a cdimage
install of WebClient instead of the web-based install.


PSC00281096 : Cannot install WebClient using 64-bit Internet Explorer
================================================================================
The web-based WebClient installation fails if you use 64-bit Internet Explorer
(IE).

This is a third-party limitation. The workaround is to either use 32-bit IE or
use the cdimage install.


PSC00255748 : WebClient 11.2.x service pack fails to install on top of
WebClient 11.2.0
================================================================================
The WebClient 11.2.x service pack fails to install on top of WebClient 11.2.0
for a non-admin user. It is a problem in the WebClient 11.2.0 installation and
cannot be resolved in this service pack. To install the WebClient 11.2.x
service pack properly, you must uninstall the WebClient 11.2.0 and install the
WebClient 11.2.x service pack again.


PSC00238578 : Slow Webclient installation on the Windows 2003 Server
================================================================================
While installing WebClient on the Windows 2003 Server, the progress bar might
reach 90% and then stop for more than 20 minutes before completing the
installation. This happens for the non-administrator group users.



i. LANG

PSC00353905 : Unique FIND BEGINS on multi-component index
================================================================================
A unique FIND BEGINS on a table returns an exact match if:

- The field used for BEGINS is the only component in an index.
- There is a record where the field exactly matches the BEGINS string, and
there are other 
   records where the BEGINS string is a substring of the field value used for
BEGINS.

Note that if the index involved does not have the BEGINS field as the last
component, the unique FIND BEGINS no longer returns an exact match in the same
circumstances. 


PSC00350793 : Ambiguous field references with ICU database
================================================================================
ABL code compiled against databases with an ICU collation might now fail to
compile with an ambiguous field reference, e.g.:

** baz is ambiguous with Foo.Baz and Bar.Baz_and_more (72)

This happens if the field reference is:

- Not qualified with a table name
- An exact match for a field in one table
- A partial match for a field in at least one other table

This error is expected behavior. In previous releases, the same code compiled
against a non-ICU database also fails to compile when correctly detecting the
same ambiguous reference.


PSC00347977 : Extra attempts to write a modified buffer after an error
================================================================================
If a buffer is scoped to a transaction block and is updated in that block, the
AVM should try to save the update when the block ends.	In addition, if other
modifications are attempted (to that buffer or another one) in a
sub-transaction and there is an ERROR or STOP condition, and that condition is
handled by either DO ON STOP or DO ON ERROR (e.g., DO ON ERROR UNDO, LEAVE),
this part of the modification is undone, but the AVM should still try to save
the original record update at the end of the transaction.  

However in previous releases, there were many circumstances where because the
ERROR or STOP condition in the sub-transaction block was not being cleared
properly, the AVM did not try to save the original record update at the end of
the transaction block.	As of OpenEdge Release 11.7, the original record update
is written as expected if these same sub-transaction conditions are not cleared
properly.

There are additional conditions that cause a buffer to be saved before the end
of the transaction block.  For example, an update to a primary key field is
saved immediately. Also, the AVM attempts to save a record if ABL code reads a
new record into the buffer or executes an explicit RELEASE.  In such cases, you
do not notice this early saving of transaction updates because the record has
already been saved by the time the transaction ends.


PSC00334772 : Pathnames may be misinterpreted as single line comments (SLCs)
================================================================================
Single line comments (SLCs) in the language are have the // syntax, which is
the industry standard. 

However, there are statements in the ABL that accept pathnames as arguments,
and because there is no requirement for these paths to be quoted, there is a
possibility that an unquoted and hardcoded pathname (such as a UNC path) in
code that may be interpreted as a single line comment.

These are the known statements that are affected by SLCs:

ò	RUN
ò	COMPILE
ò	OS-APPEND/COPY/CREATE-DIR/RENAME
ò	INPUT
ò	OUTPUT
ò	INPUT-OUTPUT
ò	SYSTEM-DIALOG GET-DIR/FILE

Example:
RUN //rdlserv/foo/bar.p.

In this case, the path is interpreted as an SLC, and the RUN statement uses the
code on the next line as its arguments. This can be fixed by quoting all
pathnames.  Quoting pathnames is considered a good coding practice.


Also, double slashes are only interpreted as SLCs when located at the start of
a string; they are not interpreted as SLCs if they are in the middle or end. 

Example:
RUN not/an//SLC.

This statement functions just fine.


PSC00183853 : INT64 ActiveX support requires oleaut32.dll version 5.1.2600.2180
or later
================================================================================
To support the INT64 data type with ActiveX controls or ActiveX Automation
objects, C:\windows\system32\oleaut32.dll must be version 5.1.2600.2180 or
later. If your application uses an earlier version of oleaut32.dll and a
parameter of INT64 data type is passed to a COM object, the results are
unpredictable and may result in an error.  This is true even if it is an input
parameter and the number in the variable is within the 32-bit range. 
Therefore, if you do not have the correct version of oleaut32.dll, you should
not use the INT64 data type with COM objects.


PSC00183705 : CAN-FIND X field-list problem
================================================================================
The FIELDS clause does not guarantee the inclusion or exclusion of fields in
any given query.  The AVM may require additional fields, for example, to
accommodate a key value required by an index. 

The application must not reference fields that are missing from the FIELDS
list. In most cases when this occurs a run-time error is generated indicating
that the field is unavailable.	

There are situations, for example with a function that includes the NO-ERROR
attribute by default, where it is legitimate to satisfy function requirements
using a field that is missing from the FIELDS list.  Such instances equate the
missing field to the Unknown value (?) or null (for DataServers) in order to
resolve an expression. In other words, the ABL attempts to detect missing
fields with run-time errors where possible. However, there are cases where it
is legitimate to use a missing field in an expression.	In such cases, the
missing field will resolve to the Unknown value (?) or native data source null.


PSC00132307 : Thai characters or complex scripts do not display in command
prompt or console
================================================================================
The Windows command prompt or console is not enabled for Thai characters or
complex scripts on any version of Windows (XP, Vista or Windows 7). 

When trying to read Progress messages sent to the Console while using Thai or
complex scripts, you can:

* Use the English PROMSGS file.

* Use the Thai character or complex script PROMSGS file, but pipe the output of
the command to a file that can be read by any Windows editor.


PSC00322779 : More restrictions on read-only class-based object properties
================================================================================
In previous releases, ABL allowed changes to the values of read-only object
properties when the change was implemented using a built-in function. These
functions included SUBSTRING, ENTRY, LENGTH, OVERLAY and EXTENT when used to
modify a read-only value, for example, SUBSTRING(obj:pubProp, 1) = "ABC".  It
also included functions that updated the value of a MEMPTR, such as PUT-BYTE
and PUT-STRING.  

This was clearly incorrect behavior. As of OpenEdge Release 11.7, trying to
update a read-only property in this way results in an error.


PSC00151150 : Data types defined in imported schemas are not seen by the WSDL
Analyzer
================================================================================
RN# 100B-00168
==============
When a WSDL document contains an XML Schema import element, the type and
element definitions of the imported schema are not seen by the analyzer. A
message indicating that a type or element definition could not be found will
appear in the generated documentation.

To work around the problem, copy the xsd:schema elements (and their contents)
from the imported schema document and paste them as children of the wsdl:types
element of the WSDL document.  Also comment out the xsd:import element in the
xsd:schema element.

Double check namespace prefix definitions to ensure the prefixes in the copied
XML are properly defined and can be referenced by other parts of the WSDL
document.


PSC00148993 : Time zone information lost for some XML datatypes
================================================================================
The XML Schema datatypes, gYearMonth, gYear, gMonthDay, gDay, and gMonth, can
optionally include time zone information. OpenEdge does not handle time zone
information correctly for these datatypes. Any time zone input from the ABL is
lost, as is any time zone information returned from a Web service for these
datatypes. For example, "1953-10-4:00" becomes "1953-10". The one exception is
that Z, indicating Universal Time (UTC), is retained (for example, 1953-10Z
remains 1953-10Z).



j. Management

PSC00348945 : Asynchronous rules will no longer fire alerts when the watch that
has ôAlertsEnabledö is set to FALSE.
================================================================================
In OpenEdge Management, the asynchronous rules such as AppServer Start may fire
alerts even when the alerts are disabled in an active monitoring plan of a
resource. However, threshold rules do not fire alerts when the alerts are
disabled. Both asynchronous rules and threshold rules should respect the
ôAlertsEnabledö setting of a monitoring plan, but this inconsistency was a bug.

This has been fixed, and the asynchronous rules will no longer fire alerts when
the alerts are disabled in a resourceÆs active monitoring plan.


PSC00183176 : Cannot use an older version of PROMSGS with a current release
================================================================================
Through the automated OpenEdge installation process, Progress Software
Corporation programmatically ensures that a current OpenEdge installation has
the most recent OpenEdge messages in the PROMSGS files. This approach also
ensures that you always have the most up-to-date OpenEdge messages throughout a
release's lifecycle. Do not perform any tasks related to the PROMSGS files
outside of the standard OpenEdge installation process; for example, attempting
to use the PROMSGS environment variable to point to any versions of a PROMSGS
will have unpredictable and potentially undesirable results.

For example, the PROMSGS files installed with the OpenEdge Release 10.1B
contain the most up-to-date messages to support OpenEdge Release 10.1B. After
the initial install, if you install add-on products, or OpenEdge install
service packs that are released after the 10.1B Release date, Progress again
programmatically provides any newer or more recently updated PROMSGS files. As
each OpenEdge add-on product or service pack is installed, the installation
program checks to ensure that the newest copy of the PROMSGS file is being used
by all products.


PSC00183170 : ubroker.properties file is renamed ubroker.properties-sav
================================================================================
When uninstalling the ubroker.properties file is renamed ubroker.properties-sav
and copied to the temp directory.


PSC00183166 : Uninstall fails with Proshell.dll in use
================================================================================
The uninstall will fail if any of the files it attempts to remove are busy. Use
of the Windows Explorer has been known to cause this condition of rendering the
file proshell.dll as in use, thus preventing the uninstall from succeeding,
however this may not be obvious at the time of uninstall.


PSC00183165 : Error when default language not installed
================================================================================
While adding a product after an installation has been completed, if you attempt
to change the default language to a language that has not yet been installed,
and the language is one listed with a non-alphabetic character (e.g. -, (, ))
an error will result that will not accurately reflect the problem. The error
should not prevent the installation from continuing. This problem is apparent
on Windows/NT platforms only and does not occur on UNIX.  The workaround is to
select entries from the pull-down list boxes.


PSC00183164 : Random errors with mapped drives on MSW
================================================================================
Due to Microsoft Knowledge Base Article #294816, we do not support the running
of a Progress application from a Network Share for Windows Terminal Services or
Citrix running on Windows 2000 Server or Windows 2000 Advanced Server. Terminal
Services users might see errors like "Disk or Network Error" or "C0000006: In
Page I/O Error", etc.  This happens due to a bug in the Windows operating
system where the network redirector only creates one file control block (FCB)
for all user connections so that when the first user to open the files logs
off, the other users lose their connections to files. To work around this
problem, place the program and associated data files on a localvolume, not
network share.


PSC00349930 : ContainerOffline alert is no longer fired when a new remote
AdminServer is added.
================================================================================
When a new remote AdminServer is added to OpenEdge Management, a
ContainerOffline alert is fired and cleared as soon as the status of the remote
AdminServer is changed to online. This alert is no longer fired.


PSC00354718 : Batch Program may overwrite log files when multiple processes are
running for it.
================================================================================
When multiple processes are running for a Batch Program, all the processes
write to the same log file. In such cases, each process appends to the same log
file and overwrites messages written by other processes. To avoid this, use a
parameter substitution in the log file name to separate each process to write
to its own log file.


PSC00343399 : SQL exception inserting process data into trend database with
long executable paths
================================================================================
A SQL insert exception may occur when recording trend data when the path to an
executable, or the number of arguments passed to the executable, exceeds the
SQL field width for the trend database Sys_Process.Process_Command or
Sys_Process.Process_CommandArgs fields.

The exception normally looks like this, followed by the actual insert
statement:

An SQL Exception occured during an insert into the trend database.  SQLState:
HY000
   message: [DataDirect][OpenEdge JDBC Driver][OpenEdge] Character string is
too long (8184)
   vendor: -20152

To work around the issue use dbtool or the data dictionary SQL width tool to
increase the SQL width of the Sys_Process.Process_Command and
Sys_Process.Process_CommandArgs fields to a value large enough to hold the
entire command and arguments.

Alternatively, apply the delta named fathom1161.df for the 11.6.1 release
located in $OEM/db/110/db.zip file.  This delta contains field width changes
for above mentioned fields.


PSC00182944 : Working with Properties
================================================================================
If an "-Xbootclasspath:", "-Xbootclasspath/a:", or "-Xbootclasspath/p:" entry
that contains spaces in the argument value has been added to the
JavaTools.properties or AdminServerPlugins.properties file, the entry must be
manually edited to include quotes around the entire argument value (value to
the right of the colon).



k. NETUI

PSC00348858 : Mapping the Unknown value to null in .NET
================================================================================
Before 11.7, if you set a .NET property that is defined as a mapped type (a
base type) to the Unknown value (?), the property's value would be set to the
default value for its data type.  Starting in 11.7, the value will be set to
null if the property in .NET is defined as a nullable type.  For example, the
string data type in .NET is always nullable, so you will get null instead of
the empty string.  

Other base types are not nullable by default, but they can be explicitly
defined as nullable in .NET with special syntax.  If you set a property of some
base type other than string to the Unknown value (?), it may be set to null,
depending on how the .NET class was written. Most often these types are not
nullable and the behavior will be the same as it was before, i.e., you will get
the default value for the data type.

If you need to revert to the old behavior, you can use the startup parameter
"-unknownToNull 0".  Using the parameter with a value of 1 or greater gives the
new behavior. The default value for the parameter in 11.7 is 1.

Note that when you pass the Unknown value (?) as a parameter to a .NET method,
the target variable in .NET is already set to null if the type is nullable. 
This new behavior is only for setting properties.

This new behavior is also available in 11.6.3, but unlike 11.7, the default for
-unknownToNull is 0. To get the new behavior in 11.6.3., you need to opt into
the new behavior by using "-unknownToNull 1".


PSC00210591 : ABL extended .NET object subscribed to .NET event may be deleted
prematurely
================================================================================
An ABL extended .NET object subscribed to a .NET event may be deleted
prematurely. This bug can be observed with the following conditions:

- An ABL extended .NET object, for example AForm, is visualized
- A .NET object, for example a button, is not on AForm, but one of AForm's
methods is subscribed as the handler for this button's event.
- There is no reference to the button in the implementation of AForm.

If AForm is closed, and therefore Disposed, and all ABL references to it go
away, it will be deleted even though the button subscription is still
outstanding.  In other words, the button, in essense, still has a reference to
the class due to the subscription.  But this does not prevent the object from
being deleted, as it should.


PSC00225230 : .NET controls that call back to the AVM on other threads are not
supported
================================================================================
A .NET object might call back to the AVM to handle an event on a thread other
than the main UI thread. It might also call a .NET method on a different thread
where the method was overridden in a derived ABL class. The AVM does not
support either of these cases and raises a run-time error if they occur.


PSC00204789 : Workaround for Elegant Ribbon control and
IsolatedStorageException
================================================================================
The Foss PersistentStateManager class automatically loads and saves application
state in isolated storage, which does not work with the current OpenEdge .NET
bridge architecture.  Setting the
LoadStateAutomaticallyFromIsolatedStorageForDomain property to FALSE disables
the automatic state loading at application startup.  Add this line to your
program that uses the Elegant Ribbon control:

Elegant.Ui.PersistentStateManager:LoadStateAutomaticallyFromIsolatedStorageForD
omain = FALSE.
 
This also means that changes in the quick access toolbar will not be loaded and
saved automatically.  If you need the changes to be kept, manually use the
Load() and Save() methods of the PersistentStateManager class.


PSC00353880 : The Infragistics UltraPaletteInfo control does not work
================================================================================
Infragistics has introduced a new UltraPaletteInfo control that is contained in
the Infragistics assemblies provided with OpenEdge 11.7. You cannot use this
control. If you drop this control onto a GUI for .NET form, it results in the
following compiler error:

"Extent of array parameter to method or function GridPaletteRow does not match
what is expected. (14823)"



l. Next Gen AppServer

PSC00355399 : To enable APSV set statusEnabled=1
================================================================================
If the statusEnabled property is set to ô0ö in the openedge.properties file,
Java Open Client cannot run and APSV is disabled. Set statusEnabled=1 in the
<ablapp>.<webapp>.APSV section of the openedge.propeties file to enable APSV.
Note that statusEnabled=1 is the default.


PSC00354276 : Insufficient privileges error when creating an instance on
Windows
================================================================================
If you create a PAS for OpenEdge instance from an account without administrator
privileges, two error messages stating, "You do not have sufficient privilege
to perform this operation" will be displayed. These messages are caused by
attempts to create symbolic links to the oepropCmd.bat and deploysvcCmd.bat
files so that oeprop and deploysvc can be called through tcman as an external
command. Without administrator privileges, each attempt to create a link fails
and an error message is generated after each attempt.

Microsoft has loosened security requirements in this regard in the upcoming
Windows 10 Creators update. Once this update has been installed, these symbolic
links can be created without an Administrator account.


PSC00354333 : Deleting AgentInfo tables causes a GPF
================================================================================
If an ABL program deletes any of the table handles returned by static methods
from Progress.ApplicationServer.AgentInfo, any subsequent attempt to
update/reference those tables causes the agent to generate a General Protection
Fault (GPF).


PSC00351686 : Progress.Data.BindingSource not supported on PAS for OpenEdge
================================================================================
The Progress.Data.BindingSource class is not safe for multi-session use and is
therefore not supported for use on the Progress Application Server for
OpenEdge.  Attempting to use it results in the following error:

	 BindingSource is not supported on multi-session server. (18967)


PSC00344668 : PAS for OpenEdge localhost_access_log filling with messages
================================================================================
If you are running Developer Studio with a PAS for OpenEdge instance, you may
notice the localhost_access_log.<date>.log filling with  "oemanager" and other
messages.

Developer Studio continually queries the instance to make sure it is still
available.  There may be as many as 60 messages in a minute.  This causes the
localhost_access_log.<date>.log file to grow quickly.  Also, it makes the file
hard to parse when you are trying to debug an issue.

With the 11.6.1 release of OpenEdge, a filter is available that excludes the
"oemanager" queries from being logged in the localhost_access_log.<date>.log
file.

To enable the filter:

1) Go to your <pasoe-instance-dir>/conf directory.

2) Save a backup copy of the server.xml file

3) Open the server.xml file and add the line [ conditionUnless="oemanager" ] in
the AccessLogValve section. For example:

	    <Valve className="org.apache.catalina.valves.AccessLogValve"
	       directory="${catalina.base}/logs"
	       prefix="localhost_access_log."
	       suffix=".txt"
	       conditionUnless="oemanager"
	       pattern="%h %l %u %t &quot;%r&quot; %s %b %D" />

4) Stop and restart your PAS for OpenEdge  instance to enable the change.

5) Verify that	the "oemanager" queries no longer appear in the
localhost_access_log.<date>.log file.


PSC00348215 : JSDO Catalogs not protected by default in PAS for OpenEdge
================================================================================
In the classic Application Server REST Adapter, the Spring Security Stack is
configured to provide authorization protection for JSDO catalogs. Under PAS for
OpenEdge, catalogs no longer are in their own directory. Adding wildcard
protection to.json files would protect the JSDO catalogs and all other .json
files. If you want to target JSDO catalogs for protection, you need to add
specific intercept URLs to your Spring Security configuration file. 

For example, to protect a catalog file named Customer.json, perform the
following steps:

1. Open the WEBAPP/WEB-INF/oeablSecurity*.xml file you are using for your
WEBAPP.

2. Look for this section:
	<!-- Restricted Mobile session class uses this page as part of its 
	      login() operation, protect it so it triggers user 
	      authentication --> 
	<intercept-url pattern="/static/home.html" 
			access="hasAnyRole('ROLE_PSCUser')"/>
	<!-- Mobile application restrictions section ends here -->

3. Add the following intercept URL to the section:
	<intercept-url pattern="/static/Customer.json" 
			access="hasAnyRole('ROLE_PSCUser')"/>

Do this for all catalogs you want to protect.  



m. OEBPM

PSC00355633 : OpenEdge BPM is certified to run on JDK/ JRE 1.7.
================================================================================
In OpenEdge release 11.7, though OpenEdge servers are certified to run on JDK/
JRE 1.8, OpenEdge Business Process Servers (BP Servers) are certified to run on
JDK/ JRE 1.7 only. 

As a result, please note the following:
 - BP Servers, both EJB and Portal servers, which use the environment variable
JAVA_HOME are now directed to the folder location $DLC\oebpm\jdk.
- Though Progress Developer Studio for OpenEdge runs on JDK/ JRE 1.8, the
custom Java components that are to be deployed to BP servers such as adapters,
Java libraries added to BPM ProjectÆs classpath, and Business Objects must be
built to run on JDK/ JRE 1.7 only. 


PSC00255318 : BP Server DataSet CLOB supports only UTF-8
================================================================================
When you populate a dataslot with a DataSet value that contains a CLOB field,
the format must be UTF-8.

If your source data is in another format, it must be converted to UTF-8 using
ABL statements.  

For example:

COPY-LOB FROM m2 TO etable1.clobFld CONVERT TARGET UTF-8.


PSC00353424 : 32-bit OpenEdge BPM Server does not start on Windows 10
================================================================================
When OpenEdge BPM is installed on Windows 10 using a 32-bit installer, the BPM
Server does not start due to Java heap space limitation.

As a workaround, perform the following:

1. Open the oebpsejbenv.cmd file located in the %DLC%\oebpm\jboss\bin
directory.

2. Locate the line similar to the one mentioned below:
set VM_ARGS=%VM_ARGS% -server -Xms256m -Xmx1024m -XX:PermSize=128m
-XX:MaxPermSize=256m 

3. Change the values in -Xms256m -Xmx1024m to -Xms128m -Xmx512m.

4. Start the EJB server using the command startEjbServer.cmd.

5. Open the oebpsportalenv.cmd file located in the %DLC%\oebpm\jboss\bin
directory.

6. Locate the line similar to the one mentioned below:
set VM_ARGS=%VM_ARGS% -server -Xms256m -Xmx1024m -XX:PermSize=128m
-XX:MaxPermSize=256m -Dnet.sf.ehcache.sizeof.filter=ehcachefilter.config
-Doebps.server.name=%SERVER_NAME% -Doebps.server.type=%SERVER_TYPE%
%PROCESSOR_ARGS% %EXT_VM_ARGS%

7. Change the values in -Xms256m -Xmx1024m to -Xms128m -Xmx512m.

8. Start the Portal server using the command startPortalServer.cmd.


PSC00256482 : EJB Server does not start when hostname has _
================================================================================
The EJB Server does not start when hostname has "_". This is a limitation with
JBoss application server.


PSC00355889 : Invalid DateTime and Password validations for JSDO Catalog.
================================================================================
When validating the dataslots supported by JSDO Catalog, invalid semantic
annotations are generated for CHARACTER dataslot with Password, DateOnly, and
DateTime validation types.

As a workaround, for DATETIME dataslot, 
ò	Replace semanticType : { "type" : "Date" } with semanticType : "Date"
ò	Replace semanticType : { "type" : "Datetime" } with semanticType :
"Datetime"

And for CHARACTER dataslot with "Password" type, add semanticType : "Password".



n. Open Client

PSC00351494 : Java OpenClient connection problem with http proxy
================================================================================
The default Java Open Client has connection problem with HTTP proxy after JSSE
upgrade. The recommended workaround for this is to use RSA instead of JSSE as
SSL provider.
Use bilow env variable to set this, before running the Java open client. 
PSC_SSL_PROVIDER=rsa


PSC00194437 : Multiple Dataset parameters and RPC Encoded Web Services with
Axis java clients
================================================================================
When defining multiple INPUT DATASET parameters for an OpenEdge web service
that uses RPC/Encoded SOAP messages, do not use the NAMESPACE-URI option in the
DEFINE DATASET statements.

The Apache Axis Java client does not properly handle namepsace declarations
when it sends a SOAP request for an RPC/Encoded SOAP message. As a result, an
incorrect message will be sent and the data will not be loaded into the
ProDataSet.

Axis may have similar issues with OUTPUT DATASET parameters. SOAP responses
from OpenEdge may not be properly deserialized.

If the NAMESPACE-URI option is required, then either define the OpenEdge Web
service to use Document/Literal or RPC/Literal SOAP messages. Alternatively,
avoid the issue by passing only one dataset in the Web service call.



o. PDSOE

PSC00293941 : ABL UI Design wizard does not have entries for Structured
Procedure, Structured Include, Method Library and SAX handler
================================================================================
The procedure type templates (Structured Procedure, Structured Include, Method
Library, and SAX handler) supported in standalone AppBuilder are not available
in Progress Developer Studio UI Designer Wizard.  

You can make the Developer Studio recognize the ABL source files without an
user interface as AppBuilder files in case they are SmartObjects that have
super procedure that one wants to override using Add Procedure or Add Function.
The SAX handler is a SmartObject, but Structured Procedures or Structured
includes are not. A Method Library may possibly inherit from a super procedure.
    

Here is a workaround to make the Developer Studio recognize the SAX handler as
an AppBuilder file. This approach should also work for the other templates, but
the file will be created as a .w, which may not be desirable.	 
--
For the SAX handler, the template can be selected manually via following steps
from the first page on New UI Design Wizard:
1. Click the Template button.
2. Select the src\adm2\template directory from the directory drop down.
3. Select the sax.w template from the selection list.

For a permanent workaround, you can change the src/template/shared.cst to
define the sax handler as a SmartObject as follows:

Replace:
-----------------------------------
*NEW-PROCEDURE	SAX &Handler
-----------------------------------
with:
-----------------------------------
*NEW-SMARTOBJECT  SAX &Handler
-----------------------------------

You can specify and add your own .cst files the same way as the standalone
AppBuilder. They can also be added from the Palette in the Developer Studio.


PSC00354459 : The CatalogGeneration task does not work on certain platforms
with database fields defined with LIKE
================================================================================
For platforms other than Windows and Linux, the CatalogGeneration task does not
work properly if the project source depends on database fields defined with the
LIKE option.


PSC00240112 : Progress Developer Studio for OpenEdge debugger behavior change
================================================================================
Starting in OpenEdge Release 11.0 and continuing in all subsequent releases,
the Progress Developer Studio for OpenEdge debugger does not stop at the first
executable line. Instead, the debugger stops at the line containing the first
breakpoint. 


PSC00355862 : Classes in the PROPATH are not listed in wizards
================================================================================
When you restart a work space, ABL files added in the PROPATH are not listed in
wizards such as Test Case Class or Super Class Selection wizards and in code
assistance.

Workaround is to launch the Developer Studio with clean option (Use Developer
Studio - Clean).


PSC00355412 : Installing Progress Developer Studio 11.7 to an external Eclipse
4.5.2
================================================================================
If you try to install Progress Developer Studio 11.7 plug-ins directly to an
external Eclipse 4.5.2 using the integrateArchitect.bat file, it does not work.


Here is a workaround to install it successfully:
1. Go to
${external_eclipse_Location}\plugins\org.eclipse.platform_{version_details}.
2. Open the plugin.properties file.
3. Modify the entry version to 4.5.2\n\ (such as {1} ({2})\n\)
For example: 
Version: 4.5.2\n\
4. Save the file and run the integrateArchitect.bat file. 


PSC00355813 : Web UI project incremental publish not working properly if
Powershell version is 2.0
================================================================================
You cannot incrementally publish a Web UI project if the Powershell version is
2.0. As a workaround, you must upgrade the Powershell version to 4.0.

Following are the steps to check Powershell version:
1.Launch command prompt and type the powershell command and press Enter.
2.Type the $PSVersionTable.PSVersion command.	
You will see the following result: 

PS C:\Users\abc> $PSVersionTable.PSVersion

Major  Minor  Build  Revision
-----  -----  -----  --------
4      0      -1     -1

If the major version as in above is less than 4, incremental publish of Web UI
project will not work and an upgrade is required.


PSC00346799 : The 'Publish changes immediately' option in the New OpenEdge
Project wizard for the 'ABL Web App' project type is removed
================================================================================
When creating an OpenEdge project of type "ABL Web App", the Publish changes
immediately option is no longer supported to publish the ABL services.

After you have created your project, your project and the ABL services are
published depending upon the server instance settings in the Server editor. You
can modify the publishing settings in the Server Editor to one the following
options:
ò	  Never publish automatically
ò	  Automatically publish when resources change
ò	  Automatically publish after a build event

You can also set the publishing interval in seconds.


PSC00222815 : OpenEdge cannot compile/run GUI for .NET code generated using
previous versions
================================================================================
When you edit GUI for .NET code generated using a previous version of OpenEdge
Visual Designer, the generated code is updated to the current version of
Progress Developer Studio for OpenEdge and thus might contain new language
constructs that are not compatible with the previous versions of the compiler
and the AVM, and wizard-generated code is incompatible with previous versions.
Such incompatibilities can occur even when you do not explicitly use new
features of the current version.

To compile and execute GUI for .NET code, you must use an OpenEdge version that
matches or is later than the Visual Designer version in which the code was
generated. For example, source code opened in version 11.0 of the Visual
Designer will no longer compile in 10.2B.



p. Porting

PSC00187938 : On Windows 7 platforms, sqlexp only works with a numeric port
number
================================================================================
On Windows 7 platforms, sqlexp can only connect to the database server if the
port is numeric. For example, specify the port number as 4050 rather than using
a service name like "mydbservice".


PSC00208050 : Windows 64bit requires more memory for the Operating System
================================================================================
In Windows 64-bit, the operating system requires 2GB of free memory for
acceptable OpenEdge performance.  If your shared memory allocation (database
buffer pool) leaves less that 2GB of free memory, then performing even simple
operations, such as finding a record that is in memory, is extremely slow.



q. REST Adapter

PSC00305570 : Unable to deploy REST application on a remote AdminServer
================================================================================
You might observe timeout issues when deploying a REST application on a remote
AdminServer. This can happen when large REST application zip files take longer
to deploy than the default request timeout allows.  Work around this issue by
increasing the fathom.activemq.timeout Java system property value.  By default,
this property is set to 20000ms.  Increase the value to 40000ms by adding
"fathom.activemq.timeout=40000" to the $DLC/fathom.init.params file.



r. Replication

PSC00254497 : Parameters must be in sync on source and targets
================================================================================
The following startup parameter values on target database(s) must be greater
than or equal to the values on the source database:
* Lock Table Entries (-L)
* Number of Users (-n)
* Maximum JTA Transactions (-Maxxids)
* The sum of Number of Users (-n) and Maximum Servers (-Mn)

In this release, if the values for the target are less than the source, the
agent will not start, a message is written to the log file, and a failure
return code is sent to the server.
In prior releases, a message was written to the log file, but the agent
started.



s. SQL

PSC00334862 : Unordered output for DISTINCT without ORDER BY
================================================================================
In 11.6 as part of the new optimizations done in the SQL, most of the DISTINCT
queries are now transformed to GROUP BY to get significantly better performance
in most cases. 

For example, 

Assume the user given query as below 

select distinct  c1,c2 ,c3 from Test_tabl1; 

Is now transformed internally as 

Select c1,c2,c3 from Test_tabl1 Group By c1,c2,c3; 


Side effects of the above transformation : - 
--------------------------------------------------- 
1) 
Without any transformation, DISTINCT is performed by Sorting the input rows.
Because of the SORT the output will be in the sorted order of distinct columns.
 Since the query is not having any ORDER BY clause, it is not required to give
the output in the sorted order. But due the distinct operation user will see
the sorted output even though there is no order by clause in the query. 

With the new transformation, GROUP BY query is processed with HASH aggregation,
because of the HASH aggregation output results may not be in the order. 

If user wants the output to be in the sorted order, user must specify the ORDER
BY clause in the query. 

2) 
Above same ordering problem manifested in a different way. 

Select distinct top 10 c1,c2,c3 from test_tabl1; 

for the above query without any transformation, first distinct operation is
performed on the result of distinct, TOP 10 rows are returned. Since the
DISTINCT needs sorting, the TOP 10 rows will be the first 10 rows in the order.

In a way the above query returns the output equivalent to below query 

Select distinct top 10 c1,c2,c3 from test_tabl1 order by c1,c2,c3; 

Whereas with the new transformation, DISTINCT is transformed to GROUP By, if
GROUP BY processed with HASH aggregation, the input to the TOP operation may
not be in the sorted ORDER hence results would vary (i.e the output may not be
the first 10 rows). 

To get the consistent output it is required to have ORDER BY clause.

Note that the sql standards have no requirement that the output of SELECT
DISTINCT need to be in any particular order.


PSC00313386 : OE SQL supports OS authentication
================================================================================
OpenEdge SQL now supports password authentication of users based on a user-id
defined to the operating system's user identification system. This capability
is called OS authentication.

To use this capability, there must be an OpenEdge authentication domain defined
in the database schema.
Currently, a domain is defined in the ABL Data Administration tools.
Use the methods described in the Basic Database Tools section of the ABL
documentation to define the domain.

To use the domain when connecting to SQL, connect with the UserID set to
"username@mydomain"  where "mydomain" is the new domain you have defined in
Basic Database Tools.


PSC00192905 : ESQLC clients can specify an IP protocol version using a
parameter
================================================================================
ESQLC clients will default to using the IPv4 protocol.	The use of the IPv6
protocol can be selected by using the -ipv6 parameter on the connection URL. 
The following is an example of a connection URL where the use the of IPv6
protocol is desired:

progress:T:myhost:5000:mydb:[-ipv6]

The parameter is also available to use with connection URLs for the SQLDump,
SQLoad, and SQLSChema utilities.


PSC00354786 : Validation using SAN in certificates is not supported by OE JDBC
drivers
================================================================================
Providing Subject Alternative host Name (SAN) as part of JDBC connection for
validation does not work as desired. Connection to OE database using JDBC
drivers succeeds even with an invalid certificate subject name when Subject
Alternative Name (SAN) extensions are provided in the server certificate. 


PSC00354020 : Wild card CA signed certificates are not supported by OpenEdge
Database drivers
================================================================================
If a Certificate Authority signed certificate contains wild cards, the
connection to OpenEdge database using ODBC and JDBC drivers is not supported.
This problem occurs when the "hostNameInCertificate" parameter is specified in
JDBC URL to validate the hostname used in certificate.


PSC00351156 : LENGTH() now supports varbinary datatype
================================================================================
The LENGTH() scalar function can now be applied to a value of the varbinary
datatype.


PSC00350468 : Authorized Data Truncation feature used with OUTPUT option
returns an error when used with scalar functions with character values
================================================================================
When the SQL feature, Authorized Data Truncation, is used with the OUTPUT
option, and SQL queries using character valued scalar functions such as UPPER
or RTRIM,  the query execution may return an internal error as follows:
     SQLState=HY000
     ErrorCode=-219901
     [DataDirect][OpenEdge JDBC Driver][OpenEdge] Internal error 2 (A buffer
other than the NLS system 
     handle memory was not large enough) in SQL from subsystem NLS SERVICES
function nlsUpperCase 
     called from nc_upper on . for . Save log for Progress technical support.

This occurs only when the scalar function is applied to a value which is larger
than its defined size. For example, a column defined as varcahr(15) and a value
of size 45 characters is read.

The scalar functions with this behavior are: 
Translate, Initcap, Insert, Lower, Ltrim, Prefix, Replace, Repeat, Rtrim, Upper

The scalar functions Substr() and Concat() are not affected by this problem.

This problem will be resolved in the next OpenEdge service pack.


PSC00287562 : Error in data access for TP table when data is not moved to
respective partitions.
================================================================================
For a TP table data access, user may experience failure when one or more
partitions of that table are marked for "prepare for split target". If query
tries to access such partitions, SQL server returns failure saying "Table is in
maintenance mode". This happens for local index scan and table scan. However,
SQL server does not report error when Global index scan is used by the query.
Below is the illustration of above behavior using an example.

--Alter the table to a list partition table
ALTER TABLE TAB
PARTITION BY LIST I USING TABLE AREA "tpCustomerArea60"
(
   PARTITION PART_1 VALUES IN (1),
   PARTITION PART_2 VALUES IN (2)
)USING INDEX IDX_TAB;

ALTER TABLE TAB PREPARE FOR SPLIT PRO_INITIAL  TARGET PARTITION PART_1;

--Access data using local index scan. Error shall be thrown in this case
SELECT * FROM TAB;
 [DataDirect][OpenEdge JDBC Driver][OpenEdge] Table "HUM.TAB" cannot be
accessed for a partition (partition id=1) due to incomplete db maintenance
operation.(17736)

--Access data using global index scan. We shall get result in this case
SELECT * FROM TAB FORCE(INDEX(INDX_GLOBAL)) WHERE I>0;
	  I
-----------
	  1
	  2

So, it is recommended  to the users to move the data to respective partition as
soon as the partition is marked as "prepare for split target" to have
unrestricted data access. 


PSC00355314 : Env variable to increase Java object cache size
================================================================================
Sql server may return error "[DataDirect][OpenEdge JDBC Driver][OpenEdge] Error
in Stored Procedure Execution. (7853)" when a large number of stored
procedures/User defined functions are executed. This error can happen when Java
object cache size is not sufficient. 

Environment variable PROSQL_JCACHE_SIZE can be used to increase the size of the
sql  Java Object cache, which is used for Stored Procedures and User Defined
Functions and sql triggers.

Usage:
export PROSQL_JCACHE_SIZE=n, where n can be any integer between 1 and 10000.

Size will fall back to default 200, if a value is used which is outside this
supported range of 1 to 10000.


PSC00355165 : User Define Functions performance impact
================================================================================
SQL has introduced a new feature called User Defined Functions (UDF) in
OE11.7.0.  UDFs are JAVA based functions which run on a JVM (Java Virtual
Machine), and they are a subject for ongoing development work. Use of UDFs in a
SQL statement may impact the performance of that statement. 


PSC00354884 : REVOKE with option GRANTED BY ANY_USER
================================================================================
The REVOKE statement has optional syntax, GRANT BY ANY_USER which should revoke
all the privileges for a particular user for all users who have granted
privileges. This option is not revoking all the privileges that should be
revoked.

As a workaround, every user who granted privileges should issue an ordinary
REVOKE for the privileges that the user had granted.


PSC00188599 : SQL uses the Java compiler to compile the Java code for a CREATE
================================================================================
TRIGGER or CREATE PROCEDURE statement. If the Java compiler finds errors in
this Java source code, it outputs diagnostic information describing the errors.

A subset of the diagnostics is appended to the SQL-92 error
message which is returned to the client.

The entire set of diagnostics is stored on the server, in the SQL server's
WRKDIR (working directory).  If the owner of the stored procedure/trigger is
OWNER, and the procedure/trigger name is XX, then all the diagnostics are in
the file named OWNER.XX.LST in the SQL server's WRKDIR (working directory).

The WRKDIR is identified by either the environment variable $WRKDIR, or on NT
by the registry entry WRKDIR under the registry key for the Progress software
installation.



t. Security

PSC00353845 : Memory leak in OpenSSL upgrade
================================================================================
The OpenSSL version shipped with OpenEdge 11.7.0.0 exhibits a one-time
initialization memory leak of 64 bytes in the PAS for OE multi-session Agent
and OpenEdge SQL server executables.

OpenEdge does not expect this memory leak to impact existing applications, and
will correct the problem in the next release where the OpenSSL version has been
updated.

Resolution
------------
Fixed as part of upgrading the OpenSSL product version in all OpenEdge 11.7
service pack releases.

Reference
------------
 
http://openssl.6102.n7.nabble.com/libcryto-1-1-leaks-since-old-locks-are-remove
d-td64631.html


PSC00326921 : OE client instance is started without a ô-sslö
================================================================================
When an OE client instance is started without a ô-sslö startup parameter and
sends connection request to proserved DB running in ssl mode, the DB server
fails to authenticate the client connection request and message is returned to
client to send back a ssl enabled handshake request. If the client is capable
of sending an ssl request, the connection can be established.


PSC00355207 : OE Authentication Gateway AIX needs stsdomreg run
================================================================================
When install OpenEdge Authentication Gateway, the oeauthserver installed with
the product will start out-of-the-box except on AIX.  The domains.keystore
created for the oeauthserver is not compatible with AIX.

This is easily worked around.

1) after installing the OpenEdge Authentication Gateway
2) go to the ~/oeauthserver/webapps/ROOT/WEB-INF/config
3) here run the stsdomreg to create a new domains.keystore
	proenv> stsdomreg domainsks.csv domains.keystore
4) now the oeauthserver will start without error



u. Server Technology

PSC00335833 : Connecting to a WebSphereMQ via ABL Client using Connection
Factory
================================================================================
You must use WebSphereMQ's binding mode to connect to WebSphereMQ server
remotely. For this you will need to create the connection factory that is
compatible with jms session type used in the ABL client.

Following are the compatible combinations:

* ABL jmsSession can connect using WebSphereMQ's ConnectionFactory,
QueueConnectionFactory or TopicConnectionFactory
* ABL ptpSession can only connect using QueueConnectionFactory
* ABL pubsubSession can only connect using TopicConnectionFactory 


PSC00222182 : 11.1 Webspeed Messenger installation behavior for Windows
================================================================================
From OpenEdge Release 11.1, Webspeed Messenger installation behavior on Windows
has changed as below: 

1) WebSpeed Messenger only installation: 

The msngrs.properties file is created in the $DLC/properties location. Prior to
OpenEdge Release 11.1, it used to be the ubroker.properties file. This changed
behavior on Windows is now consistent with the behavior on UNIX. 

Note: You can rename the msngrs.properties file to the ubroker.properties file.

2) WebSpeed Messenger only installation performed over the existing OpenEdge
installation: 

a) The msngrs.properties file is created in $DLC/properties. 
b) The existing ubroker.properties file is saved as ubroker.properties-sav. 

This changed behavior on Windows is also consistent with the behavior on UNIX.


PSC00167391 : OpenEdge installation may install Sonic client and container code
================================================================================
RN#: 101A-00291
===============
The OpenEdge installation program may install Sonic Software client and
container code depending on which products get installed. Because the
installation program requires it run as ROOT on Unix, the Sonic Software code
gets installed with those permission set. The OpenEdge installation program
attempts to set the correct permissions for the Sonic code, but this fails on
the HPUX operating system. This will cause the Sonic ESB Container created by
OpenEdge to fail when started unless it is started as ROOT. To resolve this,
run the command 'chmod -R o+w *' from $DLC/sonic as ROOT.



v. WebClient

PSC00310993 : WebClient install fails from IIS
================================================================================
If hosting the WebClient install image on an IIS Web Server, the install might
fail with the following error message:
An error (-5005 : 0x8007000d) has occurred while running the setup.

If this occurs, make sure to define the following MIME types in your IIS web
server and re-try:
.inx	 application/octet-stream
.ini	 application/octet-stream
.hdr	 application/octet-stream


PSC00345578 : MD5 will change with class references
================================================================================
If a .p, .w, or .cls file references any element from another class, the MD5
value generated by COMPILE xxx GENERATE-MD5 will change when it is first
compiled in this release, even if no source code changes were made.  This means
there may be a larger download after the first new WebClient deployment after
this recompile.


PSC00183963 : DLLs and EXEs That You May Have to Package with Your Application
================================================================================
Be aware that there may be Progress-supplied DLLs or EXEs that are used by your
application that are not included in the end-user WebClient install. Strictly
speaking, these DLLs and EXEs are not a part of ABL.  For example, there are
some DLLS or EXEs only used during application installation. Other DLLS may be
required if the application uses procedures in the adecomm or adeshar
directories that indirectly use DLLs.  Progress  excludes them from the install
to keep WebClient as small as possible and keep install time over the Internet
as fast as possible. If you need any of these files to install or run your
application, include them in your application installation.  Examples of these
files are listed below by category.

DLLs used by some procedures in adecomm/adeshar:
    dirsrch.dll
    fileinfo.dll
    proprint.dll

EXEs that could be used by your install:
* ini2reg.exe
* regsvr32.exe (A Microsoft tool usually, but not always, installed on a
typical PC)



w. WebSpeed

PSC00355533 : Dropping support for WebSpeed ASP Messenger
================================================================================
Support for the WebSpeed ASP (WSASP) Messenger is being dropped as it is based
on technology that is no longer supported by Microsoft.



LIST OF ISSUES ADDRESSED

a. ADE Tools and Runtime

PSC00354001
=============================
OpenEdge.Net.URI:encode is encoding tilde characters 


PSC00352357
=============================
HTTP Client Execute() throws input-blocking error when used in ABL.NET (or
potentially any interactive session that has a WAIT-FOR in place) due to an
additional WAIT-FOR in the OpenEdge.Net.ServerConnection.ClientSocket class.


PSC00351612
=============================
Inaccurate "All the changes were backed out" error when loading (.df) file with
the option "Commit Even with Errors" is checked. 


PSC00350851
=============================
The PauseBetweenRetry option delays the first execution of an OE HTTP client
request.
The delay matches the value set to PauseBetweenRetry. 
If the value of PauseBetweenRetry is equal or greater than that of
RequestTimeout, the client will return error 408 - Request timeout.


PSC00350770
=============================
The OpenEdge.Core.String:isQuoted()  method introduced in 11.6.3 incorrectly
returns NO when a string is properly quoted, if the string has any multi-byte
characters in it.


PSC00350767
=============================
The OpenEdge.Core.String:isQuoted()  method introduced in 11.6.3 fails with
error "** Starting position for SUBSTRING, OVERLAY, etc. must be 1 or greater.
(82)" if an empty string is passed in.


PSC00350612
=============================
Using basicauthentication and viaproxy definitions on the HTTP Client to get to
the external network through the customers Proxy loses Authentication.


PSC00349532
=============================
Verify Data Width report using Format is incorrect for some negative values.


PSC00349030
=============================
Despite database security administrator being enforced, a regular user can
still delete and create data in _sec-*-role* tables


PSC00348817
=============================
In OE 11.6.2 when creating an incremental dump file involving a field for which
the case-sensitivity has been changed, a 565 error is thrown and the change to
case-sensitivity of the character field is not present in the delta.df.

 Error Message: ** FIND FIRST/LAST failed for table _Index-Field. (565)


PSC00348566
=============================
The Data Administration tool should not dump hidden system tables.


PSC00348113
=============================
All of the Add methods that have the list index as the first parameter raises
error (142) when used.
This applies to
1. Add(s as integer, o as Object )
2. AddAll(s as integer,c as ICollection)
3. AddArray(s as integer, o as Object extent)


PSC00347927
=============================
A SmartToolbar placed in a SmartWindows has its instance properties set to
"VERTICAL" and the SmartToolbar is placed on the right side of the SmartWindow.
The SmartWindow is saved, and closed, but when the SmartWindow is reopened, the
SmartToolbar is placed to the left of its original place. If the developer
replaces the SmartToolbar to the right side of the SmartWindow and saves it,
when the SmartWindow is reopened, the same problem occurs. Then the de veloper
moves the SmartToolbar to resize it and then places the SmartToolbar to the
right side of the SmartWindow and saves it. Now, when the SmartWindow is
reopened, the SmartToolbar is placed in its correct place. 


PSC00347886
=============================
When an instance of IHttpClientLibrary is re-used between HttpClient instances,
the subscriptions to the underlying ABL sockets read handler are not
reestablished and the socket times out.


PSC00347850
=============================
When using multiple AppServers with different partition names, but the same
AppService name, attempting to reconnect to the AppServer partition will
connect to the wrong AppServer if the other partition with the same name is
already connected.


PSC00346060
=============================
Some HTTP client responses are returned as Memptr objects when JsonObject
objects are expected


PSC00346021
=============================
HTTP client environment variable SSL_SOCKET_READ cannot be changed in
application code


PSC00344810
=============================
It is not possible to receive a large HTTP response in OpenEdge 11.5.1 when
using the HTTP Client.


PSC00344784
=============================
The HTTP Client truncated the request body if the body is larger than 30000
bytes for text/string bodies.


PSC00344734
=============================
HTTP classes in the ABL fail if the server uses NIO


PSC00344121
=============================
Method URI:Encode percent encodes using lowercase hexadecimal digits. According
to RFC 3986 page 12, 
"For consistency, URI producers and normalizers should use uppercase
hexadecimal digits for all percent-
   encodings.". URI:Encoding should be using uppercase hexadecimal digits for
percent-encodings.


PSC00343882
=============================
When using the RequestBuilder to post a request, and setting the ContentType to
"application/xml", one must absolutely provide an XML object (X-DOCUMENT widget
handle) as the payload. 

No such validation should occur and RequestBuilder should accept a generic
Progress.Core.String as the payload regardless of the assigned ContentType.

For example:

	oReq = RequestBuilder:Post('http://httpbin.org/post', 
						      new
WidgetHandle(hXmlDoc))			  
					      : ContentType('application/xml') 

					      :AcceptAll()
					      :Request.

The problem arises when a 3rd-party provider of a web service requires
something slightly out-of-the-ordinary. UPS Quantum View requires the payload
to be two concatenated XML documents and this is not possible with the forced
validation. 

The error that is returned when using a String is below:

Object '<xmlstring>' (of type OpenEdge.Core.String is not of type
OpenEdge.Core.WidgetHandle


PSC00343353
=============================
Error 26 and 142 when starting the Data Administration tool while connected to
more than 32 databases.


PSC00342893
=============================
If combo-box contains a question mark in one of its entries and .w file is
saved, closed and reopened in AppBuilder or PDSOE, question mark (?) is quoted.
The question mark ? is replaced with "?" and that collides with other quotes
being used in the DEFINE VARIABLE phrase, so .w file then cannot be compiled
until quotation marks are removed. 


PSC00342545
=============================
If a database has a defined Security Administrator and it has Runtime
Permissions Checking enabled, other users who do have admin rights but are not
security admins can not load .df files. 
The admins for whom the .df load breaks can still modify the schema via data
dictionary tool.


PSC00342466
=============================
Memory leak in several OpenEdge.Core & OpenEdge.Net classes including 
OpenEdge.Core.ByteBucket and OpenEdge.Net.ServerConnection.ClientSocket.


PSC00342462
=============================
Error (26) while trying to generate an incremental .df file in the Data
Aministration tool when the source database has more than 2048 tables.


PSC00342365
=============================
Error (12702) when connecting to a database from the Unix Data Dictionary if
Disallow Blank UserId Connections is enabled


PSC00341672
=============================
Reconstruct Bad Load Records utility has not been updated to handle .d files
larger than 2GB. 


PSC00341124
=============================
Filter generated by TelerikPushNotificationMessageBuilder.cls is incorrect


PSC00333868
=============================
Various miscellaneous memory leaks were encountered when running
OpenEdge.Net.HTTP code.  This resulted in a significant memory leak in
applications that call into this code repeatedly.



b. Adapter for SonicMQ

PSC00349366
=============================
User receives a warning message when calling beginSession procedure to start a
connection to HornetQJMS using generic JMS adapter


PSC00341405
=============================
Clients seem to be randomly crashing when receiving a SonicMQ message.



c. AdminServer

PSC00347955
=============================
Enhancement PSC00249549, raised PICA from 8192 to 1000000 (10.2B08, 11.2, and
11.3)
The maximum value has not been raised in the AdminServer/OE Console:
Invalid value for "Database service communication area size" property. Range is
4 .. 8192. Default is 64


PSC00336452
=============================
OpenEdge Management does not recognize / respect environment variable PROCFG.


PSC00308125
=============================
default and exceptions.log files do not follow WRKDIR



d. AppServer

PSC00350317
=============================
AppServer Agents crash when attempting to connect to an Oracle DataServer
broker (command-line (_probrkr) or Unified) with the Agent logging level
(srvrLoggingLevel) set to 3.


PSC00348894
=============================
Slow network performance when making appserver calls in all unix machines.


PSC00347184
=============================
AppServer stuck in SENDING.  Trying to delele file descriptor with bad file
handle number.


PSC00345750
=============================
Unable to specify a domain on an ABL (apsv) connection to PASOE.


PSC00341977
=============================
AIX WPAR environment only, State-Reset Appserver agents lose connectivity with
appserver broker. Broker errors (8127) (8119). Agent errors (9407) and (140).   



e. Command Line Tools

PSC00344492
=============================
certutil -list will not display all certificates in certstore if one of the
certificate hash file names is greater than 8 characters before the .0.


PSC00344445
=============================
amdsrvc /remove fails in 32bit OE installation



f. DATASERVER

PSC00354436
=============================
CRC different when .df imported having schema-image connected versus
disconnected


PSC00353200
=============================
Protrace generated with exclusive-lock preceded by no-lock in find statement


PSC00352789
=============================
Query with OUTER-JOIN crashes client on compile/syntax check with DataServer
for Oracle.


PSC00352476
=============================
When a table has array fields in it and it's pushed to Oracle, a field is
created for every array element.  Therefore tables with a number of arrays may
tend to create in excess of 250 fields in the Oracle database.

When a query is executed against such a table from an ABL client that uses the
FIELDS phrase, it results in the following error unless the -znoposirc
parameter is used, then it works fine.

The error is below:
Field 11 from ced_facil record (recid -380) was missing from FIELDS phrase.
(3782)


PSC00352474
=============================
When running r-code while using the -znoposirc parameter the session crashes.

The following stack trace may be seen in protrace or DMP file analysis:

scBuildSignature
scrcode_to_flds
rnreloc
rnproc_entry


PSC00352309
=============================
When trying to update/add table definitions from Oracle to schema holder, the
process aborts when it encounters an invalid object instead of skipping the
invalid objects and continue adding/updating remaining objects.


PSC00351769
=============================
Issues to find records in MS SQL DataServer without NO-LOCK when MS SQL
Database has collation as Latin1_General_CI_AI


PSC00351222
=============================
System error 6227 on CAN-FIND.


PSC00350763
=============================
When querying a table that has multiple DATE fields in it (which are split into
Date and Time portions by the DataServer), the time portion will have the same
value for all fields in the record despite the times being different in the
Oracle database table.


PSC00350553
=============================
ORACLE error -907, **  missing right parenthesis, occurs when executing a query
that reference 2+ fields in its WHERE clause, one of which is referenced in a
BEGINS operator.


PSC00349635
=============================
Table null qualifier in .df causes schema migration to fail.


PSC00349342
=============================
OUTER-JOIN query returns error ORA-0905 when FOREIGN-OWNER is not defined in
the Dataserver schema for the table OUTER-JOINed.


PSC00348546
=============================
Customer is not able to run REPOSITION-TO-ROWID method more than once to the
same rowids as the sessions crashes when trying to run the method by a second
time. The join query involves temp-table and oracle table.


PSC00348458
=============================
The presence of extent fields in a dynamic query join causes the client to
hang.


PSC00348392
=============================
Query with large WHERE clause fails to compile with errors 1458 or 1453 against
DataServer products. The same query compiles as expected against an OpenEdge
database.


PSC00347822
=============================
In OE 11.6.1 QUERY-TUNING HINT in a static query returns an empty string on
Windows or random characters on Linux. The behaviour does not occur in 11.3.


PSC00347050
=============================
DataServer crashes on FIND EXCLUSIVE-LOCK NO-ERROR NO-WAIT.


PSC00346949
=============================
Oracle DataServer uses incorrect owner schema


PSC00346185
=============================
MS SQL server data server Invalid cursor error upon Indexed reposition


PSC00346183
=============================
Session crashes on index-reposition


PSC00346096
=============================
Error 1397 and 4212 occur during DataServer undo operation


PSC00345639
=============================
Dynamic query running against DataServer for MS SQL Server returns the wrong
results when joining on an array element.


PSC00345570
=============================
Dynamic query with OUTER-JOIN returns the wrong number of records using the
Oracle DataServer


PSC00344532
=============================
Character Client does not raise warning message for failed table pull with
DataServer for Oracle.


PSC00343737
=============================
Oracle Dataserver doesn't recognize database disconnection with CAN-FIND


PSC00343644
=============================
COPY-LOB corrupts schema XSD output when copying from a longchar in a UTF-8
session connected to the Oracle dataserver using NLS_LANG=.AL32UTF8


PSC00343525
=============================
Connection attempt fails with error ORA-01017: invalid username/password; logon
denied when using an encrypted password and the oech1:: specification with
DataServer for Oracle.


PSC00343437
=============================
Oracle DataServer generates SELECT statements that have an invalid syntax near
the OR phrase.


PSC00343349
=============================
A joined FOR EACH (or OPEN QUERY) statement with a WHERE Variable = ? OR
DB-Field = Variable
will return the following error

[Microsoft][SQL Server Native Client <version>][SQL Server]Incorrect syntax
near the keyword 'OR'.


PSC00342972
=============================
Query using FIRST_ROWS hint fails in OpenEdge 11 when executed with a
client-side join.


PSC00342501
=============================
Dynamic query with many tables get missing field error when all fields are in
field list


PSC00342413
=============================
Memory allocation failure reported from the SQL Server Native Driver after
running an ABL code with large transaction scope.


PSC00342402
=============================
COPY-LOB corrupts schema XSD output when copying from a longchar in a UTF-8
session connected to the Oracle dataserver using NLS_LANG=.AL32UTF8.


PSC00341960
=============================
pro2ora created a character field as NCLOB insead of LONG if the character
field size is 2000 or more.
field detail:
ADD FIELD "NAME" OF "NAME" AS character 
  DESCRIPTION "NAME"
  FORMAT "X(16000)"


PSC00341946
=============================
Static query with FIELDS return error 3782 using _probrkr with DataServer for
Oracle when there is a client selection


PSC00341777
=============================
Oracle single-shot server-side query very slow compared to the same query
executed with server-side joins disabled.


PSC00341729
=============================
protrace generated with dynamic query with 11.4.0.026 on solari64 and hpuxia64


PSC00341715
=============================
ORACLE errors due to extra characters being generated in SQL query by the
DataServer.


PSC00341428
=============================
ORACLE error -907 see "ORACLE Error Messages and Codes Manual". (1252) -- **
missing right parenthesis


PSC00340518
=============================
Get a system error 49 when using OF, but if you put the fields instead of the
OF, then it works fine:

find first WLGM no-lock.
 
for each usergroupd where domain = "Domain1" no-lock,
first UserWLGM of WLGM
	 where UserWLGM.ActUserID = UserGroupD.UserGroup
	       no-lock:
message "yes".
end


PSC00339675
=============================
When using a dynamic query that features a LOOKUP function against DataServer
for Oracle, the LOOKUP is not taken into account and the result-set is not
filtered.


PSC00336319
=============================
In the OpenEdge 11.5 DataServer for Oracle, table 30 (Required Oracle
permissions) contains two errors :
* sys.cclol$ while it should read: sys.ccol$
* sys.link$1 while it should read: sys.link$ (1 should be in superscript)

These entries in the table were fixed.


PSC00336284
=============================
Dumped .df from an Oracle schema holder gets corrupted and can't be loaded back
to a new wmpty database.


PSC00335463
=============================
Non-standard LOGICAL default values are not migrated as Constraints with the
OpenEdge DB to MS SQL Server utility (PRO2MSS). Default values for such fields
are omitted.


PSC00333127
=============================
Error 1436 when compilation foreign owner is different than run time foreign
owner.


PSC00332262
=============================
Incomplete sql generated for an outer join query having OF and WHERE clauses -
Oracle error "933-** SQL command not properly ended".


PSC00332261
=============================
ORACLE error -936 can occur due to incorrect SQL generated for an outer join
query having OR condition, OF and WHERE clauses.


PSC00332199
=============================
Oracle error "933-** SQL command not properly ended" when WHERE and OF phrases
are used in outer join query.


PSC00332186
=============================
Oracle error "920-invalid relational operator" when WHERE clause filter has
additional parenthesis around predicate of an outer join query.


PSC00332162
=============================
Oracle error "904-Invalid Identifier" when outer join query having WHERE clause
with additional brackets.


PSC00332161
=============================
Oracle error "907-** missing right parenthesis" is getting displayed when WHERE
clause filter has additional parenthesis for an outer join query.


PSC00332158
=============================
Incorrect SQL generated with OUTER JOIN queries that contain OR condition in
the WHERE clause.


PSC00332138
=============================
Single shot join query with OR condition in WHERE clause is giving Oracle error
936-** missing expression


PSC00332136
=============================
Oracle error "936-missing expression" when use WHERE clause filter with
additional parentheses in a single shot join query.


PSC00332135
=============================
Oracle error when use character datatype fields and additional parentheses in a
single shot join query.


PSC00332134
=============================
Oracle error "1008-not all variables bound" when add additional parentheses to
a WHERE clause filter of a single shot join queries


PSC00330379
=============================
ABL client crashes when join with dynamic query GET NEXT  with EXCLUSIVE-LOCK
running against MS SQL Server DataServer.


PSC00329702
=============================
Oracle error -918 (column ambiguously defined) when running a join query in
OpenEdge 11.


PSC00329290
=============================
Outer join query with multiple WHERE and OF phrase is crashed with protrace.


PSC00329088
=============================
Oracle error -933 when use OR condition and OUTER-JOIN in Open Query.


PSC00328066
=============================
"ORACLE error-1008 - not all variables bound" error with 3level join query.


PSC00325714
=============================
Incorrect parameter binding causes error ORA-01007 in application when using
the default -c.


PSC00322883
=============================
Foreign owner in query's inner select is different from Oracle user name used
in SchemaHolder connection string.


PSC00322751
=============================
Executing dynamic query, referencing 2 separate tables, via DataServer for
Oracle returns the wrong results due to incorrect ORDER BY clause


PSC00315200
=============================
Oracle DataServer trims the white spaces from the varchard2 characters when
retrieved from oracle using stored procedure call.


PSC00314885
=============================
DataServer for Oracle generates a SQL statement that uses the wrong parameter
in a sub-SELECT statement resulting in an Oracle error. Disabling server-side
joins (NO-JOIN-BY-SQLDB) avoids the issue.


PSC00313797
=============================
Oracle DataServer returns error ORA-1008 "Not all variables bound" on nested
FOR EACH...WHERE...FIRST clause


PSC00312183
=============================
If the port range for _probrkr is 5 digits for -dsminport and -dsmaxpoort, ABL
client shows error 5049 during the connection to foreign database. The _probrkr
starts fine but when client tries to connect using the _probrkr to foreign
database, it gives the error.


PSC00310780
=============================
Oracle DataServer generates wrong SQL query with missing bracket.


PSC00305727
=============================
Incorrect results with single shot ABL query when the query has both USE-INDEX
and BY clause.


PSC00288880
=============================
Executing a FOR EACH ..., OF, query where the WHERE clause on the second table
contains an OR operator does not all results.


PSC00261840
=============================
Updating a logical database field using Oracle Dataserver gets the following
error:
** Input value: <input> should be <yes/no>. (87)


PSC00209281
=============================
OE blob field is created as not null on Oracle when using protoora. The Oracle
Guide is modified and this information is updated in the "Preparing a database
for the utility" section in Chapter 7.



g. DB

PSC00355202
=============================
IDXBuild fails when using -datascanthreads with any value higher than 1.


PSC00355005
=============================
STS remote connection request can cause database server to fail and crash if
the database is auditing enabled and if there is a user holding an exclusive
table lock on _Db (usually doing schema changes) due to locking conflict.


PSC00354391
=============================
A database running with -B2 crashes with error (1040) SYSTEM ERROR: Not enough
database buffers (-B)


PSC00353905
=============================
find unique BEGINS statement on multi-component unique index returns a value
when it should return ambiguous.

When using 64 bit rowids or global indexes of table partitioning the BEGINS
does not properly identify exact matches returning value when it should not and
is not returning values when it should.


PSC00353516
=============================
dbtool option 3 (record validation) issues superfluous "ERROR - skip tables
expected offset" for valid records that have more than 16 fields but no
associated skip table.


PSC00353500
=============================
Calling a CAN-FIND statement causes the _mprosrv process to eat up 100% of cpu
indefinitely and locks up the client during this process.

This issue occurs on unique indexes where previous entries matching the BEGINS
criteria have been deleted.

When the problem occurs the client is locked and cannot be killed short of
'kill -9'.  The _mprosrv stack trace (using progetstack several times)
indicates that the process is spinning in the index manager.


PSC00353232
=============================
DBTOOL, 6 Record Fixup reports LOBS LT 10 bytes:
Warning - first record fragment of <recid> area <area> is only 9 bytes, LOB 1.

This is a false warning, therefore it cannot be fixed either by dbtool, D&L or
re-importing the LOB.


PSC00353090
=============================
When converting the codepage of a database where auditing has been disabled,
proutil -C convchar convert utf-8 on a 1252 database errors with (14323),
(3970), (3968), (3943)


PSC00352822
=============================
When using the -basetable or -baseindex parameters, queries against the
_tablestat or _indexstat tables respectively will return incorrect records.


PSC00352111
=============================
Using the move schema utility (mvsch) in 11.6 can corrupt 'special' schema
indexes.

'special' schema is present when any of the following features are enabled:
- Auditing
- Key events
- Transparent Data Encryption

The problem was introduced in version 11.6.0 and is present in the service
packs.
It is fixed in hotfix 11.6.3.011 and 11.7.0


PSC00351720
=============================
When using auditing, the user sees the following error messages from any record
create/update/delete operation:

    Not enough room in auditing record
    Failed to put field 14 in table -300

If the caller is an ABL client, the user will also see the following error,
then a core dump:

   SYSTEM ERROR: rmmak failed, retcode=-1 (1106)

The calculation for splitting the audit data to multiple records is incorrect,
in some cases resulting in trying to write to a single record when it should
have been split.


PSC00350688
=============================
There is an issue with indexes that span the 32/64-bit boundary.  

You are vulnerable if the High Water Mark of the area containing the index is
above 2 gig.  To determine your HWM, use prostrct statistics on areas
containing indexes.
If the number of active blocks listed by the prostrct statistics for an index
area is greater than (2^31 / [area records per block]) the area is susceptible
to this defect.

Error messages that indicate that you may have the issue include:

SYSTEM ERROR: Attempt to read block 0 which does not exist in area <area
number>, database <path/dbname>. (210) (14684)

SYSTEM ERROR: Index x (table-name,index-name): couldn't find key <nnnnnn> recid
nnnnnnnnn  (8783)


If you are vulnerable, you can determine if you actually have the problem by
using index check option 3.


PSC00350589
=============================
After moving table data to another partition, the original composite initial
partition still shows the same amount of records when using 
proutil databaseName -C partitionmanage view table theTableName status


PSC00350208
=============================
Database not accepting shared memory(mpro) connections returning error 49 when
connecting to a database with large -B


PSC00350171
=============================
Enabling Table Partitioning on a database that already has Auditing enabled
causes problems when the Table Partitioning auditing event is not already
loaded into the database.  The Enable Table Partitioning command does not issue
any errors, but it does not appropriately cope with the missing auditing event.
 

Subsequent commands using the BI are affected, including truncate, idxbuild,
and proserve.

To work around this issue, perform the following steps by loading of the
auditing events and enable Table Partitioning by:
1) Disable Auditing
2) Enable Auditing
3) Enable Table Partitioning


PSC00349896
=============================
Using mvsch on LOB data returns 'Failure in large object operation, error = -8'


PSC00349765
=============================
It may occur that a JTA enabled database, that has pending transactions when it
crashes, is not able to be restart, showing the message:

BROKER: rlLockApply: failed to acquire lock ret -1217 1 


PSC00348776
=============================
When analyzing records containing LOB fields, an error similar to the following
is raised:

SYSTEM ERROR: Unexpected error -16 from recGet*lobLocator table 4 ptn 0 ppos 5
lobObjId 7480621 (18152)


PSC00348482
=============================
auditreconfig command to relocate audit tables is not returning and consuming
25-30% CPU.


PSC00347999
=============================
PROREST -verbose output reports estimates of negative time and negative percent
complete. Inspite of these the backup volume is restored.  This occurs in the
11.6.x release.


PSC00347545
=============================
On a replication-enabled database, executing prostrct builddb, followed by an
index build, the index build will crash with a memory violation error (49).


PSC00346416
=============================
After a bulk load operation, the proutil -C idxbuild does not detect inactive
indexes if choosing "By Activation" then by "Choose inactive indexes".


PSC00346392
=============================
When using object level assignments to the alternate buffer pool (-B2) the
number of buffers actually put into the alternate buffer pool is larger than
the collective size of the objects.


PSC00345401
=============================
When dbtool option 3 encounters a corrupt record with a negative rowid, the
database crashes during WDOG cleanup.


PSC00345397
=============================
dbtool option 3 returns inconsistent results when validating by single table or
area versus validating all tables.


PSC00341928
=============================
Old free blocks (created with version 9 of Progress) may have issues with
incorrect checksum errors due to an older issue with block format differences
from a version 9 database.  

Free blocks with checksum issues should therefore be ignored when being read.


PSC00341209
=============================
There is a difference in behavior when building word indexes. idxbuild, idxfix,
and mtidxbuild build empty word indexes. 
However mtidxbuild does not turn on the active bit in the schema if the index
for a non-empty table is empty.  

The index can be activated:
    online using idxactivate
    offline using idxbuild or idxfix


PSC00314434
=============================
After running the code page conversion process the database's .lk file is left
behind


PSC00259124
=============================
DBTOOL reports a SQL width error where none exists, for a table containing
extended characters (e.g. Chinese) where the number of characters is equal to
the current SQL width.


PSC00207159
=============================
Binary load table with UTF-8 CLOB crashes session
****************************************
When attempting to binary load a table which contains a  UTF-8 CLOB the binary
load session crashes



h. Diagnostics

PSC00345627
=============================
OpenEdge Debugger can't find or create the debug listing for an r-code file an
error is thrown in the program being debugged. 
Stepping into a method call with no source or debug listing file available,
while attached to a remote process in the debugger, causes the method to return
error. 


PSC00341949
=============================
When a source code file includes an encrypted include file and the include
reference is preceded by one or more spaces, the debugger source code pane
truncates the debug listing near the end of the encrypted source.



i. Doc

PSC00343201
=============================
The -SQLLockWaitTimeout parameter is now documented in the 11.7 version of the
OpenEdge Data Management: Database Administration guide  and the OpenEdge
Deployment: Startup Command and Parameter Reference guide.


PSC00206069
=============================
Dataset/Temp-Table method doc should mention buffer state
****************************************
When executing one of the following methods on a temp-table or ProDataSet, and
the default buffer of one of the contained temp-tables is available, there is
no guarantee as to the state of that buffer after the method has finished
executing:

WRITE-XML
READ-XML
FILL
SAVE
Any Reject/Accept type activity 

The default buffer is needed for read/create/write operations and its momentary
contents are not guaranteed after the operation is completed.  

For example, table contents before and after execution of the WRITE-XML method
should be the same. However, when using the default buffer to handle the write,
its position and state are not guaranteed. A named buffer should be used if
there is any expectation of buffer availability after the method completes.  

For the other methods listed above, there is no guarantee that the temp-table
is going to contain the same data afterward, so no such expectation is even
considered reasonable.



j. GUI

PSC00351285
=============================
A fill-in-field loses its cursor position when it has a VALUE-CHANGED trigger
code block that "applies" a VALUE-CHANGED event to another fill-in field.


PSC00350940
=============================
The VALUE-CHANGED event is not generated when navigating between rows in a
browse by using the up and down arrow keys if the application uses the APPLY
statement to apply the "ENTRY" event to another widget in a ROW-LEAVE trigger.


PSC00350017
=============================
When enabling a browse widget with a title bar, the title bar may be shown as
active before applying ENTRY to it. This can result in situations in which
multiple browse widgets appear to be active at the same time.


PSC00349480
=============================
Using LOAD-IMAGE to get image from URL writes a temp-file to the Working
Directory, not the Temporary Folder when specified.


PSC00348773
=============================
Client might crash when closing a window, while processing the HIDE FRAME
statement if the frame is already hidden.


PSC00348738
=============================
GUI client may crash session when you type value into a numeric fill-in when
the cursor is at the end of the fill-in.


PSC00348144
=============================
The Procedure Editor may prompt the user to save changes when closing a file
immediately after the file has been saved. The file is saved to disk properly
but the editor incorrectly considers it to still be modified.


PSC00345658
=============================
Dynamics toolbar filter folder pages are not populated with data until the
pages are switched.


PSC00345501
=============================
ABL: The LEAVE event does not fire for the fill-in with focus when the UPDATE
EDITING statement ends because the GO event occurred.


PSC00345264
=============================
If using -cpinternal UTF-8, or a -cpinternal that does not match the
non-Unicode codepage configured for Windows, runtime errors returned from a COM
object may contain garbage characters. This happens when the COM error string
contains non-ASCII characters. For example, a COM error from a German Windows
installation may appear garbled with -cpinternal UTF-8.


PSC00345082
=============================
The 64-bit GUI client fails to load an external .DLL in the current working
directory, generating error 3258. This occurs when the current working
directory is not in the PATH environment variable.


PSC00342291
=============================
When data in a combo-box field in a browse is deleted the previous value is
restored when leaving the row.


PSC00341899
=============================
Sub program containing .ocx (for example the Progress PsTimer) goes to the
background when launching a dialog box. This happens when AppBuilder generated
code is edited, LoadControls is not executed from within the Control_Load
procedure generated by AppBuilder, 
but in the enable_UI. If the dialog box containing .ocx is opened from the
subprogram, it causes another window of the application to come to the
foreground and the dialog box is parented to the wrong window. 


PSC00329658
=============================
Application crashes on closure after calling specific procedures that involve
using REFERENCE-ONLY Datasets and Temp-Tables.


PSC00296663
=============================
The Help button on alert boxes sometimes fails to display the dialog box
showing the current ABL stack trace when the Debug Alert (-debugalert) feature
is used in a GUI for .NET application.



k. Install

PSC00349235
=============================
Silent install of PAS Developer license fails to install PAS.


PSC00347218
=============================
The Windows Registry Key is not created when only Web Services Adapter (WSA) is
installed.


PSC00346194
=============================
Cannot select OpenEdge Management install folder when adding a product which
contains OEM to a prior installation that does not contain OEM.


PSC00342601
=============================
Uninstalling Shared Network Install incorrectly deletes ODBC driver files from
DLC\bin from the main OpenEdge installation on the remote machine.



l. LANG

PSC00355233
=============================
When using a DataSet as the data source for the ProBindingSource bound to a
List & Label control, and using the reportMode feature of ProBindingSource, if
the DataSet has a 3 level (or more) hierarchy, data from the third (or 4th...)
table will be missing from the generated report.


PSC00354941
=============================
Process may hang or crash if SAX-WRITER writes to longchar or memptr, and
object is not deleted before longchar/memptr gets out of scope.


PSC00354149
=============================
Error 9245 "Unable to memory map procedure library <library name>. System call:
mmap. Error number: 12." occurs when attempting to use a memory-mapped
procedure library with the Progress AppServer for OpenEdge (PASOE) on the HPUX
platform.


PSC00354138
=============================
Client hung with asynchronous call to malloc on Linux.	This can be triggered
by sending multiple signals to the process in a very short space of time.


PSC00353987
=============================
SYSTEM ERROR: bfposto: position timestamp when using a GUI Browser and remove
all rows from one or more tables associated with the query.
To avoid this issue, reopen the query after removing the row.


PSC00353835
=============================
If prowin.exe.manifest or prowin32.exe.manifest is missing or corrupt, tooltips
will not display.


PSC00353483
=============================
Passing in a blank string as filename to OS-DELETE is handled as an attempt to
delete the current working directory. 
With the RECURSIVE option that tries to delete all files within the working
directory, which is likely to contain application code or other important
files.


PSC00353314
=============================
If a unique FIND ... WHERE <field> BEGINS "string" operator finds a unique,
exact match for the "string", it's supposed to return that record even if
there's other non-exact matches that qualify for the BEGINS operator. See also
http://knowledgebase.progress.com/articles/Article/P13261. This applies only if
the BEGINS field is the last component of an index. If it is not the last
component of the index, then an ambiguous result will be returned, even if one
record exactly matches the BEGINS field.
This behaviour was inconsistent for databases with an ICU collation. For an ICU
collation, if the BEGINS field was not the last component of the index, an
exact match would be be returned if one existed, instead of the ambiguous
result.


PSC00353108
=============================
ABL session may crash when running code that passes a dataset as an OUTPUT
parameter with the BIND option to multiple nested calls.


PSC00353011
=============================
Serialize-hidden attribute on the only temp-table field in a dataset omits
temp-table name from json output.


PSC00352775
=============================
AppServer agent agent memory consumption increases when running a request with
an input-output dataset parameter and the value passed is not set (unknown
value).


PSC00352671
=============================
In some cases, using -IOEverywhere 0 startup parameter causes WAIT-FOR ...
PAUSE to break - the pause phrase isn't honored and the code continues
immediately.


PSC00352494
=============================
Compiler fails to detect when the same buffer name is specified twice in the
DO/REPEAT FOR phrase.
Instead it will raise an ambiguity error "** <field> is ambiguous with
<buffer>.<field> and <buffer>.<field> (72)" later when a unqualified reference
to that buffer is made inside the block.


PSC00352161
=============================
The -noroutineinwhere parameter does not catch some cases such as properties
with implementation or x:y types of expressions.


PSC00352138
=============================
The character client (_progres) may crash if there are no sensitive widgets in
the session.


PSC00352127
=============================
When connected as a super-tenant to a multi-tenant database and executing ABL
code which contains query with nested TENANT-WHERE / SET-EFFECTIVE-TENANT
clauses, _Tenant records get locked, they are not released automatically and
can not be edited / deleted.


PSC00351966
=============================
Deleting a class object that contains temp-tables may cause a session crash
when the temp table archive logging is enabled. 


PSC00351903
=============================
Compiling large numbers of programs at once may cause a system error.


PSC00351562
=============================
"SYSTEM ERROR: Illegal Instruction (47)" when multiple web applications are
called concurrently using the SOAP transport on PASOE.
The XML parser implementation on the agent was not thread safe, potentially
causing a race condition situation when several client sessions were being
served by the same agent process.


PSC00351416
=============================
The ASSIGN FRAME <frame name> <field name> ... . statement causes unnecessary
index updates, if an indexed field is included in the statement but the value
was not modified in the UI.
The most noticeable effect of this is that it can make a FIND not locate the
record for a moment (a few milliseconds) in other sessions, leading to
concurrency and congruence issues. This is specific to updates in the ASSIGN
statement with the FRAME phrase.


PSC00351413
=============================
Trying to add a JSONObject to a JSONObject created from a dataset via the read
method might cause the following error:  "...Can not add a construct to its
ancestor (16072)".


PSC00351394
=============================
Data may not be returned if you run a query as Super Tenant where a
Multi-tenant table is joint with a non-multi-tenant table. For example,
assuming Order is a multi-tenant table and Customer is not:

 OPEN QUERY q FOR EACH Order TENANT-WHERE TENANT-ID() GT 0 
    NO-LOCK, EACH Customer OF Order.


PSC00351219
=============================
When copying CLOBDB data from the database to LONGCHAR variables, the longchar
final code page after the copy should be the code page specified by -cpinternal
or the fixed codepage.	But the longchar code page is set to the original
CLOBDB code page.


PSC00350793
=============================
If compiling ABL code against a database with an ICU collation, the compiler
will fail to detect an ambiguous field reference if the following are true:

- the field reference is not qualified with a table name
- the field reference is an exact match for a field in one table
- the field reference is a partial match for a field in at least one other
table

This results in the compiler selecting the field that is the exact match, when
instead it should return an error similar to the following:

** baz is ambiguous with Foo.Baz and Bar.Baz_and_more (72)


PSC00350726
=============================
A crash occurs when wrapping up an internal procedure which defines and
instantiates FRAME widgets that contain widgets which have triggers defined,
all of which was defined within the internal procedure.

For instance(pseudo-code):
PROCEDURE testProc:
    DEFINE BUTTON btnOne LABEL "One".
    DEFINE BUTTON btnTwo LABEL "Two".	 

    DEFINE FRAME testFrame btnOne btnTwo.

    ON CHOOSE OF btnOne IN FRAME testFrame DO:
	// Do some stuff
    END.

    ON CHOOSE OF btnTwo IN FRAME testFrame DO:
	// Do some stuff
    END.

    UPDATE btnOne btnTwo IN FRAME testFrame.
END PROCEDURE.	<-- Crash occurs here



PSC00350673
=============================
Using READ-XML to read data into a DATASET is causing an extra blank record to
be created in a TEMP-TABLE whose XML-NODE-NAME is the same as the DATASET's
XML-NODE-NAME after upgrading to 11.6.1.  Prior to 11.6.1 the problem did not
occur.


PSC00350651
=============================
Japanese half-width characters generate error 142 when assigned to a longchar.  


PSC00350591
=============================
A class that references it's own static members using unqualified references
(meaning: just the property/method name without the type name) fails to
compile, if it also defines a temp-table where one of the field names is an
(abbreviated) match for the class name.


PSC00350571
=============================
Using a clob field as source in READ-JSON crashes the AVM. 


PSC00350536
=============================
A PAUSE statement that shows a message (including the default one) will trigger
error "SYSTEM ERROR: -s exceeded. Raising STOP condition  ... . (5635)" if
there's no window with a status-area visible to display the message.

This is only seen under specific circumstances, if the PAUSE is in a repeating
block that executes the REPOSITION statement.


PSC00350518
=============================
Enum input parameter to method gives error "Usecount of the segment is <= 0
(14347)" when current-language is changed in the session.


PSC00350332
=============================
When a buffer is defined via DEFINE BUFFER and added to a browse, the
BUFFER-FIELD attribute of the browser's column will return the unknown value if
the DEFINE BUFFER statement is not preceded by any other definition (variable,
etc).


PSC00350164
=============================
Progress.Json.ObjectModel.ObjectModelParser:Parse() does not display an error
message about an invalid empty string if the empty string is a LONGCHAR in a
codepage that is not supported. An error message about invalid encoding is
incorrectly displayed instead.


PSC00349770
=============================
Standard ABL .w window code is not generated using AppBuilder, so ON END-ERROR
trigger is missing. .NET form is used, so ABL "WAIT-FOR CLOSE OF
THIS-PROCEDURE" is replaced by .Net WAIT-FOR  "WAIT-FOR
System.Windows.Forms.Application:Run()". When running mentioned ABL .w window
and hitting Esc key, application hangs and window is not responsive.



PSC00349763
=============================
Defining a local buffer for a reference-only temp-table that has the same name
as the temp-table / the temp-table's default buffer doesn't work correctly.
The code will compile fine, but at runtime the routine defining the local
buffer will raise error: 

"Attempt to reference uninitialized temp-table. (12378)"

on the first buffer reference, even when the temp-table is bound to an actual
instance at that time (via BIND or BY-REFERENCE parameter).


PSC00349690
=============================
When a procedure is run from a memory-mapped procedure library and the profiler
is enabled, the application crashes. If the profiler is disabled but the
memory-mapped procedure library is used, the application runs successfully. IF
the profiler is either enabled or disabled and a non-memory-mapped procedure
library is used, the application runs successfully.


KB 000064681 saws that this crashes but says little else (INCOMPLETE KB
article)


PSC00349622
=============================
Syntax check in CATCH error block causes session crash


PSC00349147
=============================
A runtime error may occur when attempting to use SUPER to call an overloaded
version of an ABSTRACT method. 


PSC00348886
=============================
Client might crash after assigning a property with a body setter via the x:y
syntax.


PSC00348674
=============================
When making a request of a external Web Service which returns a RAW value using
MIME MultiPart Message encoding, the below error occurs followed by a client
crash.

Error receiving Web Service  Response: Input stream is empty. Cannot create
XMLParser. (11773)

Stack trace shows as follows:

strcasecomp
HTMIMEParseSet_dispatch
HTMethod_deleteExtensionMethod
HTMethod_deleteExtensionMethod
HTFileInit
HTMethod_deleteExtensionMethod
Basic_proxyFilter
chttp_mem_init
HTHost_read
CHTTPStatus_new
HTHost_deleteNet
EventOrder_executeAndDelete
HTEventList_loop
request_ReceiveResponse
HttpRequest_f_receiveResponse
PSCInputMessage::getStatusCode
WASP_StubCallData::receive
WASP_DII_Call::receiveImpl
WASP_DII_Call::receive
WASP_AsyncReceipt::wait
SOAPCall_f_receiveResponse
cwsSetHandler
cwsRun
rncs_run
rnRunInRPC
rnSetupRunIn
rnrun


PSC00348610
=============================
OE 10 database server might run into the following error when OE 11 clients are
connected:

(15093) SYSTEM ERROR: This server has too many open cursors so the cursor
creation attempt by user <n> at line 798 in /vobs_rkt/src/glue/nsadb.c, msgcode
51, ROWID 0, table <n>, index <n>.



PSC00348484
=============================
An idle AppServer agent may not exit after receiving a signal (SIGTERM, for
instance) if it is connected to a database.


PSC00348445
=============================
The EXPORT statement can fail to correctly write data for malformed multi-byte
characters. This happens when the malformed data contains a byte that is
interpreted as the lead-byte of a multi-byte character, but the rest of the
data is shorter than the expected number of trail-bytes.
Malformed data can be loaded in the AVM or database when deliberately
circumventing the automatic codepage conversion provided by OpenEdge. This
issue should not occur if codepages of input data are correctly identified.


PSC00348399
=============================
Attempting to connect from an ABL client to a new Web Service that the
government in Czechoslovakia has mandated all businesses use for collecting
electronic receipts generates the following error:

Error loading WSDL document: 'mixed" attribute must not be used on the parent
of 'xs:simpleContent' declaration (11748)


PSC00348169
=============================
SAX-READER passes an UNKNOWN MEMPTR as a parameter to the CHARACTERS callback
for XML node that contain Unicode (UTF-8) characters that do not exist in
session codepage (-cpinternal)


PSC00347838
=============================
The client may crash if a frame's title is changed to a longer string at
runtime and the frame is output to a file.


PSC00347811
=============================
Error 13128 is reported when trying to read XML that includes a relation name
greater than 32 characters for a proDataSet.


PSC00347528
=============================
The ABL allows you to get a reference to an Abstract class using
Progress.Lang.Class:GetClass and then call the New() method.  This results in a
usable class instance. This should not be possible as you should not be able to
instantiate an Abstract class. 


PSC00347434
=============================
Calling LOCALTIME function causes db crash


PSC00347235
=============================
An editable browse cell may contain extra whitespace after its screen value is
modified programmatically. In some cases the extra whitespace will make the
cell appear to be blank when the user double clicks to select text in the cell.


PSC00347026
=============================
4GL BROWSE widget is not working as expected in the combination with the FIND
trigger and QUERY statement.

BROWSE widget displays duplicate records and when displaying record values from
the BROWSE widget to another frame, synchronization is lost and other record
from the BROWSE widget is displayed.
If further logic depends on the row selected in the browse, that logic can get
disrupted as well due to being presented with the wrong record(s).


PSC00346933
=============================
If a class defines a protected TEMP-TABLE using LIKE, and defines an index
using one of the fields from the LIKE table that was also used in an index of
the LIKE table itself, then a class inheriting from it will generate error
(12767) during instantiation.


PSC00346488
=============================
Shared memory client make an external SSL webservice call then timed out.
several minutes later this client will get bkioread error.


PSC00346481
=============================
If an AppServer procedure that has an OUTPUT temp-table parameter for a dynamic
temp-table uses both an unnamed widget pool and a FINALLY block which cleans up
the dynamic objects, then the AppServer agent will hang in the RUNNING state.


PSC00346419
=============================
Trying to write a class inheriting from Progress.IO.InputStream causes error
Parsing a large JSON file.


PSC00346310
=============================
The compiler currently prevents casting an object from some type to an
interface while a dynamic-cast is working without problems.


PSC00346188
=============================
When compiling a class that uses a generic collection of primitive datatypes as
a parameter, this can result in the error:

System.TypeLoadException: System.Collection.Generic.List'1[[5]] 


PSC00346178
=============================
Querying the FRAME on a browse column handle causes error 4052 "FRAME is not a
queryable attribute for <widget id>".


PSC00346146
=============================
BUFFER-COMPARE on old and new buffer with CLOB field in database write trigger
crashes the client session.


PSC00345653
=============================
When some errors are returned by the OpenEdge.BusinessLogic.BusinessEntity
abstract class, the error message is empty and the error number is 0.


PSC00345578
=============================
R-code MD5 calculation doesn't take into account changes in referenced class
types that push compilation out of sync (ie. changes that'll trigger the error
"Could not access element '<element>' of class '<class>' using object of type
'<type>' - caller compilation is out of sync with class compilation. (12882)".

For example, if a called class outputs a TABLE parameter, where the caller uses
a TABLE-HANDLE parameter instead.  If the table schema in the called class
changes, the r-code MD5 of the caller does not change.	Another example is if
the called class changes a parameter from CHAR to LONGCHAR or DATE to DATETIME
(or any other "widening" data type match), the r-code MD5 of the caller does
not change.

This means that using WebClient Application Assembler (and any custom-built
tools that rely on this) won't package and redeploy recompiled r-code, and the
above-mentioned error 12882 will show up after deployment on the user's
machines.


PSC00345543
=============================
With a .NET form running, if you bring up a .NET dialog and then try to bring
up an ABL dialog box the following error is raised:

Encountered an input-blocking statement while executing a user-defined function
or non-void method: 'Run' that is invalid within the current runtime context.


PSC00345467
=============================
OpenEdge session on Linux / UNIX hangs after running the KEYFUNCTION function
with integer value 32767.


PSC00345391
=============================
SERIALIZE-NAME option in DEFINE TEMP-TABLE statement does not handle blank
spaces correctly.


PSC00345328
=============================
When passing an object to a method as an INPUT-OUTPUT parameter where the
parameter is defined to receive an interface parameter, the runtime is unable
to match the object instance that implements and is defined as the interface
type when using DYNAMIC-INVOKE.


PSC00345298
=============================
Progress.Json.ObjectModel.JsonObject:Read() is different when using database
buffer and the temp-table buffer. Extra field in the JSON seen with the
Database buffer.


PSC00345252
=============================
The AppServer agent may crash if a serializable object is passed to the
AppServer during a debugging session in the PDSOE.


PSC00345114
=============================
The AVM crashes when executing ABL code that access large CHARACTER fields.


PSC00344996
=============================
When attempting to delete a tenant from a MT database where SQL statistics are
enabled, you may received a lock table overflow error that prevents the tenant
from being deleted.



PSC00344994
=============================
Passing a buffer handle for a database buffer to 
Progress.Json.ObjectModel.JsonObject:READ() method crashes the session, unless
the method was called for a temp-table buffer before.


PSC00344993
=============================
Calling a function within a super procedure which includes an error for
violating the uniqueness constraint in assignment to a unique index will cause
an access violation crash to occur when attempting to display the error
message.


PSC00344970
=============================
When V6Display mode is used with the 64-bit GUI Client (prowin.exe), text in
enabled fill-ins may be cut off or completely invisible. This issue occurs only
in frames with the THREE-D option.


PSC00344940
=============================
An OpenEdge client can crash executing REPOSITION-TO-ROWID in the following
circumstances:
- the query is a join across two or more tables
- the client has a client/server connection to the database
- the rowid given for one of the lower level tables in the join does not belong
to a record of that table
- the WHERE clause on the lower level table requires selection-by-client (e.g.
WHERE TRUE)


PSC00344904
=============================
The IMPORT statement will fail to upgrade the lock on a record from SHARE to
EXCLUSIVE if importing a CLOB or BLOB. This could result in failures by other
clients if updating the same LOB before the original client commits the
transaction. This might result in the following error message:
Failed to update blob field. Could not delete existing blob. (11277)
This was introduced in 11.4. It will occur in the following circumstances:
- the record containing the LOB was locked with a SHARE lock
- the IMPORT statement is the first statement to update a field in a record,
and it updates a LOB field.


PSC00344808
=============================
version 11 client will crash when online schema change applied to version 10
DB. All version 10 client is not impacted.


PSC00344561
=============================
The RUN statement cannot run a procedure which contains extended characters in
its filename.


PSC00344505
=============================
Temp-table undo attribute value over Appserver is not respected. All
temp-tables passed to an Appserver are created as no-undo. All output
parameters to the client will also be created as no-undo create by the client.


PSC00344402
=============================
Reading an unformatted XML file into a dynamic prodataset with READ-XML doesn't
work.  Some nodes of the XML are missed.  The problem does not happen when the
XML file is formatted. 


PSC00344222
=============================
Webspeed agent may experience a memory violation crash if a cgi form variable
name is greater than 32K. This can happen if input is improperly labeled as cgi
form data when it is something else, e.g. and XML document.


PSC00344157
=============================
Client logging can cause client to deadlock itself due to async signal handling


PSC00344031
=============================
WebSpeed agent terminates after processing SOAP response send with chunked
transfer encoding


PSC00343807
=============================
The file names retrieved by INPUT FROM OS-DIR may be corrupted when the file
names contain non-European characters and -cpinternal is UTF-8.


PSC00343788
=============================
The collation-sensitive MATCHES operator enabled by -collop 2 incorrectly
matched substrings with different numbers of trailing spaces. For example, "a
a" (with one space) MATCHES "*	*" (with two spaces) returned TRUE.


PSC00343527
=============================
Session crashes after running a write trigger after validation of the record
already failed in a sub-transaction.


PSC00343489
=============================
Inconsistency assigning datetime-tz to datetime/date when SESSION:TIMEZONE set.


PSC00343411
=============================
The 64-Bit ABL COPY-LOB statement successfully copies a file (FILE
->MEMPTR->FILE) when the source file size is 4499999744 bytes but fails when
the source file size 4500000768 bytes which is just 1KB larger.


PSC00343318
=============================
The message displayed by the MESSAGE UPDATE statement may be incorrectly
truncated if its length in bytes is longer than its length in columns and the
length in bytes exceeds the number of columns available to display the message
in the message area.


PSC00343029
=============================
Using the HEX-DECODE function to assign a value directly to a specific address
in a MEMPTR variable using the PUT-BYTES function causes a memory leak,
followed fairly quickly by the following error:

Unable to allocate memory for result from  function (12118)

Once this error is encountered, doing things that result in allocating memory
to MEMPTR variables, like selecting "Tools >> Data Dictionary", result in
errors like the following:

DLL procedure GetWindowRect adeshar/_taskbar.p using an uninitialized MEMPTR.
(3233)


PSC00342867
=============================
SUBSTITUTE may cause client to crash when substitution value is a large
longchar value. Raising -s value may avoid the crash.


PSC00342586
=============================
Session crashes on DELETE PROCEDURE THIS-PROCEDURE when the procedure contains
temp-tables.


PSC00342507
=============================
The READ-XML() method fails to read XML data when the XML-NODE-NAME of a
temp-table member and the dataset name are the same value.


PSC00342409
=============================
CHUI client(shared memory connection or client/server) crashing after enable
auditing on a utf8 database under 64bit OE.
works fine for 32bit OE


PSC00342313
=============================
Attributes on the COMPILER system handle, such as NUM-MESSAGES and WARNING, are
not set when XREF-XML is specified in the COMPILE statement.


PSC00342188
=============================
A comparison of an object to the unknown value fails and causes the application
to error with Invalid Handle, error number 3135. In 11.5 this works fine.


PSC00341954
=============================
The file modification time shown in the Propath ProTool tool is based on UTC
(Coordinated Universal Time) instead of the local system time zone.


PSC00341400
=============================
When trying to process and XSD using bproxsdto4gl, it fails to parse XSDs
containing nested elements of the same name.  the result is error 13032:

XML Schema does not map to a dataset definition. (13106)
Unable to create Temp-Table or dataset schema from XML Schema. (13032)


PSC00341334
=============================
If TODAY is used as the INITIAL of a NO-UNDO variable, then it is not adjusted
for SESSION:TIMEZONE.  This also applies to NO-UNDO variables with INITIAL NOW.


PSC00340741
=============================
Program calling to WedService is running out of file handles


PSC00340697
=============================
The ABL MATCHES operator does not support collations. MATCHES can fail to
return expected matches when using UTF-8 collations.


PSC00339424
=============================
The WSDL Analyzer generates a Temp-Table and ProDataset definition with an
empty namespace instead of the correct value.


PSC00338697
=============================
BUFFER parameter for PROTECTED TEMP-TABLE in a sub-class causes error 566 when
it is the same name as the TEMP-TABLE in the base class.


PSC00337250
=============================
Using ADO Stream.Read method to copy data > 32K to a ADO.RecordSet VALUE fails
in ABL with error 5890, "The parameter is incorrect". The equivalent VB6 code
works as expected.


PSC00336543
=============================
Dynamic-call to SERIALIZE-ROW as a Widget Attribute of BUFFER Customer:HANDLE
crashes with ACCESS_VIOLATION


PSC00335231
=============================
A STOP/QUIT condition that occurs in a method that overrides OnKeyDown from
.NET does not propagate up the call stack and then prevents further use of keys
in the window.


PSC00334813
=============================
RCODE-INFO:DB-REFERENCES and RCODE-INFO: TABLE-LIST is wrong if a subclass
inherits a protected temp table definition from its super class.

The RCODE-INFO:TABLE-LIST attribute contains the string ".TXS" when there are
no table references.
The RCODE-INFO:TABLE-CRC-LIST attribute aldo contains "0".


PSC00333220
=============================
Removing process events slows the application start dramatically.


PSC00333190
=============================
Possible error 49 at end of TRANSACTION block when an update to a no-undo
temp-table failed in a previous transaction.


PSC00331033
=============================
Error 891 is misleading if a code page conversion table has been added and the
table has duplicate entries. 


PSC00330934
=============================
An integer property of the Progress.Lang.AppError class ("Severity") is allowed
to be passed to a character parameter in a method. The received value in the
method is the name of the Progress.Lang.AppError property.


PSC00330226
=============================
If the ABL session's decimal seperator isn't a period ("."),
JsonObject:GetDecimal() and JsonArray:GetDecimal() ignore the decimal point in
the JSON and return wrong values.

(Note that JSON standard enforces the period as decimal point.)


PSC00328668
=============================
Calling a method that blocks by putting up a .NET dialog but is not in a
WAIT-FOR statement can cause the application to hang when the dialog is
dismissed.


PSC00327887
=============================
The performance of OOABL method calls is slow.


PSC00327727
=============================
Garbage collection doesn't work correctly for classes that have a frame
definition.  When there are no more references to the class the ABL garbage
collector should run the destructor and remove the class.  But this doesn't
happen until the session is closed for a class that defines a frame.


PSC00327396
=============================
Performance degrades when repeatedly calling a method with a static input
temp-table parameter, if the following is true:
- The class defining the method defines the temp-table without the
REFERENCE-ONLY option
- The temp-table being passed in at runtime is passed as a dynamic table-handle
BY-REFERENCE parameter.


PSC00326584
=============================
CURRENT-QUERY method returns ? when attempting to get query from a DataSet
Relationship


PSC00326541
=============================
When you set FILE-INFO:FILE-NAME as UNC path:
"\\ComputerName\SharedFolder\Folder\..", FILE-INFO:FULL-PATHNAME returns whole
FILE-NAME attribute string, instead of full path to one directory up:
\\ComputerName\SharedFolder


PSC00324066
=============================
Defining a dataset with a partial data-relation causes the session to crash.


PSC00322464
=============================
It is not possible to read an XML file with the READ-XML method into a dataset,
if multiple dataset temp tables are defined with the same SERIALIZE-NAME
attribute. The same applies for XML-NODE-NAME.


PSC00319819
=============================
Processing JSON  via READ-JSON can cause the client to crash when the Table
data is nested in the JSON files. If you un-nest the tables in the JSON file,
it works fine.


PSC00316740
=============================
When a ProDataSet is passed as DATASET-HANDLE to the AppServer, the default
SERIALIZE-NAME / XML-NODE-NAME attribute is lost. Any attempt to use WRITE-XML
/ WRITE-JSON on the ProDataSet results in an invalid XML / JSON document.


PSC00309885
=============================
When deleting a Web Services port type after deleting the service, an
appropriate error is raised, however the memory from the request is not
de-allocated, thus causing a memory leak.


PSC00309691
=============================
This problem happens when procedure A calls a static property in class B. 
Class B is inherited from class C. Classes B and C are compiled, but the r-code
for C is removed and manual changes are made to it.  When the call is made to
the static property an error occurs which is caught as a
Progress.Lang.SysError, but there is no error message available.  The following
error was expected: 'compilation is out of date (12882)'.


PSC00305778
=============================
A client with a non-UTF-8 -cpinternal can connect to a UTF-8 database and
access records that contain characters that are not in the client's codepage.
For example, characters in codepage 1251 can exist in a UTF-8 database and a
client using -cpinternal 1252 can access these records. 
If such characters are in an indexed field, the -cpinternal 1252 client will
get the following error when trying to delete such a record :
Index %s in %s for recid %j could not be deleted.(1422)
See Article 000051254


PSC00300893
=============================
The ABL COPY-LOB statement successfully copies a file (FILE ->MEMPTR->FILE)
when the source file size is 4499999744 bytes but fails with the error listed
below when the source file size 4500000768 bytes which is just 1KB larger. 

File offset  plus copy length is greater than size of file  for ''. (11330)


PSC00297700
=============================
The WSDL Analyzer does not support all required values for the final attribute
within a simple type definition. Instead it throws error 11748.


PSC00287796
=============================
OS-RENAME fails with OS-ERROR 999 when renaming a file and moving it across
partitions, e.g. from C:/tmp to D:/tmp.


PSC00261276
=============================
Hiding the browse widget in a SmartDataBrowser object causes the linked
SmartDataObject to reposition to the wrong row. In this case, the SDO is
repositioned using the rowidWhere and fetchRowIdent functions.


PSC00245389
=============================
Tilde in include file named arguments breaks preprocess listing
****************************************
When using the tilde character in include file named argument values, this can
break the preprocess listing in OpenEdge 11.


PSC00204238
=============================
The SET-READ-RESPONSE-PROCEDURE method may cause a memory leak if you call it
more than once for a given socket object that has not been deleted (such as in
a loop that connects, process and disconnects the socket).


PSC00184174
=============================
The 4GL SEEK function does not return expected values when importing from a
UTF-16 file. It will always return the offset for the end-of-file.



m. Management

PSC00354676
=============================
dbagent crashes if hostname is longer than 32 characters


PSC00353758
=============================
Errors running Database Analysis Job from a template


PSC00353642
=============================
OpenEdge Management incorrectly shows scripted database background writer
status as not licensed


PSC00352898
=============================
Getting error 500 publishing REST service from Developer Studio to PASOE.


PSC00352444
=============================
Logfile viewer page in OEM does not provide breadcrumb link to return to screen
which lead to logfile viewer page.


PSC00351738
=============================
A failure Alert fires every Poll once an Average Procedure Duration High
monitored procedure is executed, when:
- the monitored procedure does not exceed the threshold, or
- the monitored procedure is not run within the Poll interval


PSC00351490
=============================
Modifying transport enabled or disable options in OpenEdge Explorer /
Management after modifying other Progress Next Generation AppServer setting
then saving changes from the the Progress Application Server ABL application
configuration page when the server is not running may result in a corrupted
openedge.properties file.


PSC00351147
=============================
NoFileDefinition and LogFileNotFound in OEM alert during Admin server startup


PSC00351031
=============================
The base index number and base table number input fields for database
configurations do not accept negative values.


PSC00350970
=============================
SSL error 12056 when connecting client to TLSv1 enabled database


PSC00350928
=============================
Multiple script injection and cross site scripting (XSS) vulnerabilities were
found in OpenEdge Management


PSC00350419
=============================
A CPU resource for which the actual CPU is no longer visible to OpenEdge
Management cannot be disabled.


PSC00350363
=============================
If a job is scheduled so that one run overlaps the next run, the first task
object will never be marked as terminated properly, and trending for the job
will never update properly. Due to this the user will see some of the job runs
always in running state.


PSC00350249
=============================
file selection for .paar files for PASOE rest deployment in OpenEdge Management
does not parse file names properly when suggesting a name for the service


PSC00349785
=============================
The large number of CPU cores is triggering a massive over allocation of memory
by OrientDB library due to a memory allocation scheme that is unbounded, and is
based on the number of cores available to the JVM.


PSC00349740
=============================
Shutdown of AdminServer can hang if remote Web Services Adapter ping requests
initiated from OpenEdge Management fail to terminate.


PSC00349280
=============================
OEM child shared collection cannot be modified nor removed 


PSC00348731
=============================
WS_AgentUnavailable rule fires multiple alerts although the agents are
available


PSC00348717
=============================
In the ABL Web App page, clicking the link to the PAS instance returns an HTTP
404 error referencing a URL similar to
https://localhost:8810/oemanager/applications/oepas1/webapps/transports/rest/oe
services. 


PSC00348512
=============================
Setting the parameter "SSL Server ciphers" in the "Database Configuration" page
of the OpenEdge Explorer will only set PSC_SSLSERVER_CIPHERS for 4GL
connections.
The parameter for setting up the SQL related "SSL Server ciphers" in the
"Database Configuration" page of the OpenEdge Explorer interface is missing.


PSC00348432
=============================
Cannot connect to DB after restart, if SSL cipher/protocol was configured from
OpenEdge Explorer


PSC00348423
=============================
Creating a new resource in OpenEdge Management gives error "AppServer not
licensed for container." The Admin Server log file is showing error "failed to
install plugin plugin.system (7433)" along with a lengthy ads0.exp.


PSC00348028
=============================
Suppress name change alert option change cannot be saved in OpenEdge
Management.


PSC00347980
=============================
OEM triggers UnexpectedPollingException alert when evaluating rule. Error "Rule
<rule> evaluation for resource <resource> failed because systemUsedPercent
activity value was null (17983)" is seen in admserv.log.


PSC00347975
=============================
OrientDB reporting errors in the AdminServer log that provided offset is more
than size of allocated area


PSC00347487
=============================
A SchemaDeniedException is returned for the _Repl-Agent table when environment
is monitored by 116 OEM


PSC00347412
=============================
OEM Resource page cannot display resource list.
The page divisions can be displayed, but the resource list cannot be displayed.
The resources all can be displayed in My Collections in the left side menu bar,
and all can be reached, monitored and managed.
New resources also can be created successfully.
The problem is found in Chrome and IE (Specifically in IE version
11.0.9600.18283) browser.


PSC00347348
=============================
OE Management Rule Sets contain no default data while editing the record under
Library Components, Log File,  Rule Sets.


PSC00347310
=============================
Unable to kill remote appserver agent process from OEM. Fails with cannot be
killed at this time message.


PSC00347118
=============================
Appserver/Webspeed Agent Pool Summary screen sows agents using > 100% CPU.


PSC00346903
=============================
Null column value encountered in trend sample for table Sys_Process column
Process_StartTimeStamp


PSC00346764
=============================
The size of the OpenEdge Management graph cache database grows quickly and can
potentially consume gigabytes of space when monitoring large numbers of
database and appserver resources.


PSC00346548
=============================
OEM incorrectly reporting APW's as inactive.


PSC00346352
=============================
Database will not start through OEM after the convmap.cp field is modified


PSC00346334
=============================
Log Threshold settings for the OpenEdge Progress AppServer was included in
error and need to be removed.


PSC00345300
=============================
OpenEdge Management shows errors:  Error during insertion of key in index. 
Errors are related to a bug in a third party library shipped with OpenEdge
management.


PSC00344902
=============================
OEM (Linux) with a remote container (Windows) will add one extra carriage
return between each two environment variables next to each other in
ubroker.properties file after modifying the environment variables.


PSC00344798
=============================
OpenEdge Management is only showing the first 90 (of 144) ??CPU cores


PSC00344745
=============================
UnexpectedPollingException seen with fathom trend database with the following
error:
UnexpectedPollingException, Resource Name: oem1.FathomTrendDatabase


PSC00344299
=============================
The error "Content Not Found - The page you were looking for could not be
found" is displayed while trying to edit a rule.


PSC00343536
=============================
When remote adminservers are added and Appserver resource is monitored on
remote adminserver, errors are written to admserv.log when
Process_StartTimeStamp value is null


PSC00343401
=============================
Appserver resource monitoring reports an SQL overflow exception


PSC00343399
=============================
An SQL Exception occurred during an insert into the trend database.  "Character
string is too long (8184)"


PSC00343350
=============================
Adminserver/OEM's triggered RPLU messages polluting db.lg file


PSC00343251
=============================
Not possible to specify the protocol or cipher for databases that are started
via the AdminServer.


PSC00343105
=============================
OpenEdge Management throws UnexpectedPollingException on every poll


PSC00342749
=============================
bpdatacompactconfig.jsp is missing kendo import


PSC00342699
=============================
Trending of AppServer procedure activity data fails with a SQL overflow
exception after restarting the AppServer after trending data has been recorded
at least once.


PSC00341888
=============================
Restarting the managed database resource results in SQL exception in
admserv.log   Cannot insert duplicate key in object "PUB.DB_CHECKPOINT" with
unique index "Db_Checkpoint_Sample_ID". (16949)


PSC00341835
=============================
Intermittent Unexpected exception reported during resource polling for local
resource database when stopped.


PSC00341773
=============================
AppServer polling may fail to record data in the trend database if an agent has
not completely started when trend data is retrieved for any agent.  A message
will be recorded in the adminserver log file indicating this: Failed to process
trend values: null column value encountered in trend sample for table
Sys_Process column Process_UserName


PSC00333104
=============================
If you have more than one Web Service application deployed, then it is not
possible to change the active Web Service from the WSA view within OpenEdge
Explorer.


PSC00308931
=============================
AdminServer fails to re-register managed databases that are configured to
autostart if they are still running when the AdminServer is restarted.	


PSC00246534
=============================
osmetrics crashes adminserver in jniGetNumberOfProcessors


PSC00229595
=============================
If the database server does not have a chance to perform abnormal shutdown
processing, dbagent may not fire an abnormal shutdown alert back to OpenEdge
Management.  This can occur if the database server crashes unexpectedly or is
killed, such as with with a kill -9 command. 


PSC00225833
=============================
OpenEdge Explorer removes any environment variable which contains an equals
sign in the value when saving values for any OpenEdge resource type such as
AppServer and WebSpeed.



n. NETUI

PSC00352328
=============================
Closing a stacked Modal Form where the form underneath contains an embedded ABL
window causes an access violation.


PSC00351157
=============================
Infragistics grid control is throwing error "You cannot edit row when
BindingSource is not bound to a DataSource" when you change a checkbox field
and close the form by hitting the "X" in the top right.


PSC00350521
=============================
A GUI for .NET application which uses embedded ABL windows in an tabbed MDI
interface may go into an endless loop when focus is switched to the GUI for
.NET application from a third-party application.


PSC00349798
=============================
When subclassing the Telerik RADGRIDVIEW, calling the SaveLayout method of the
new class/control crashes even with nothing added to the subclass.


PSC00348858
=============================
The handling of the Unknown value (?) when setting a .Net property whose type
is a base type (mapped type) is inconsistent and so not as described in the
documentation.	The value is never set to null but is set to the default value
for that data type.  This is inconsistent with passing Unknown as a parameter
value, where the target is set to null for strings and other nullable types.


PSC00348850
=============================
The AVM may crash when the user moves the mouse over a window which contains
certain Codejock OCX controls, including XTPDockBar and XTPStatusBar.


PSC00346312
=============================
WAIT-FOR System.Windows.Forms.Application:Run() may cause a session hang in
11.6.1 when it worked as expected in 11.6.


PSC00345222
=============================
Trying to invoke a static method using Method:Invoke() gives error 15285, but
only when trying to assign the return value. Discarding the return value causes
the code to work without problems.

A valid class instance or static class name is required for dynamically
invoking a method or accessing a property. (15285)


PSC00342976
=============================
When working with Infragistics UltraLiveTileView class, attempting to reference
an element in the Groups property using an integer value (e.g.,
ultraLiveTileView1:Groups[iIndex]) causes a compilation error:

The specified indexer type does not match any type required by this object.
(13811)


PSC00339845
=============================
Error 13965 occurs attempting to access Telerik collection member.
The class Telerik.WinControls.UI.GridViewColumnCollection does not have a
default indexed property. Therefore you cannot use the syntax objRef[]. (13965)


PSC00335963
=============================
Focus may unintentionally switch to a different tab in a GUI for .NET
application which has an MDI interface with embedded ABL windows.


PSC00327889
=============================
Adding a child row in an updateable UltraGrid fails with errors 3135 and 3140
because CURRENT-QUERY returns ? instead of a valid query handle:

Invalid handle.  Not initialized or points to a deleted object. (3135)
Cannot access the CREATE-RESULT-LIST-ENTRY attribute because the widget does
not exist. (3140)


PSC00226981
=============================
.NET dialogs may cause memory leak
****************************************
.NET dialogs may not get garbage collected, potentially causing a memory leak.



o. Next Gen AppServer

PSC00355607
=============================
Sending a large number of requests on the REST transport while the server is
starting up could lead to memory issues. These requests get queued up and
executed once the server is done its boot sequence. The first REST request will
trigger parsing of the paar file. This condition will trigger multiple DOM
parses of the same file, leading to an out of memory or garbage collection
issue due to the nature of DOM parsing.


PSC00354918
=============================
The PASOE MSAgent processes show memory growth while running under high load
for an extended period of time if the PASOE instance is being monitored through
OpenEdge Management.


PSC00354315
=============================
PASOE MSAgents memory footprint steadily increases over time running SOAP
requests


PSC00354131
=============================
When multiple JAVA OpenClients attempt to connect to a PASOE server
simultaneously, they may occasionally experience a connection error indicating
"sessionPool:NoAvailableSessions".


PSC00353900
=============================
When deploying a war file to a PASOE instance, tcman was incorrectly assuming
the directory and alias would be the same, which is only true by default.
Deploy now uses the correct information while deploying.


PSC00352443
=============================
Disabling transports in OEM PAS instances will revert the changes to ABL
Application configuration. 


PSC00351869
=============================
During instance creation or registration, we were not setting the
CATALINA_TMPDIR property. This could lead to some unpredictable behavior. This
value is now being set in the scripts.


PSC00351766
=============================
The SOAP transport will not use the proper session manager when there are
multiple ABL applications installed in an instance.


PSC00351549
=============================
 Changes made to a non-default ABL WebApp configuration are not persisted
properly in openedge.properties and causes changes to become void.


PSC00350979
=============================
MSAS SYSTEM ERROR: sessid  <number> about to close UNREGISTERED socket 1131
(via libhttpsys)


PSC00350799
=============================
On heavily loaded systems, the agent is crashing when using SSL on a connection
out from an ABL Session.


PSC00350778
=============================
Error 15740 being raised on PASOE running as a service


PSC00350348
=============================
When downloading a file via a WebHandler the connection doesn't always close
properly, preventing the proper shutdown of the PASOE instance.


PSC00349793
=============================
The domain validation functionality inside the OEClientPrincipal bean returns
the following error if the domain name has a different casing than initially
defined:

Error creating client principal:com.progress.auth.ClientPrincipalException:
Domain name specified does not exist in the registry.


PSC00349666
=============================
The PASOE Agent ignores the -proxyhost and -proxyport startup parameters.


PSC00349441
=============================
A PASOE instance crashes with System Error 49 if the startup fails.


PSC00349439
=============================
A PASOE instance fails to start on HP-UX if the hostname is longer than 8
characters.


PSC00349320
=============================
Using the logging package log4net with its associated RollingFileAppender
causes the PASOE agent to crash.  The issue is that the RollingFileAppender is
not thread safe and cannot be used in a multi-session environment.  If log4net
is required, then use the RemotingAppender along with a Remote Logging Server
to consolidate logging from PASOE sessions.


PSC00349046
=============================
PAS returning code 403 instead of 401.

The Spring Security process step of generating a Client Principal for an
anonymous user id produced a condition where subsequent client requests did not
correctly recognize the client's need to login and instead would return an
access error based on the previous request's user id.  This normally happens
when configuring the OEClientPrincipalFilter to always generate and pass a
ClientPrincpal representing an unauthenticated (Anonymous) user to the ABL
application.


PSC00348889
=============================
Cookies set by OpenEdge.Web.WebResponseWriter contain a quoted path: path="/"


PSC00348758
=============================
Error 5487 can fill the PASOE log file.


PSC00346162
=============================
If ABL program makes multiple calls to set-cookie() API, PAS WebSpeed only uses
the last cookie. The other ones aren't sent back to browser client.
This works correctly in Classic WebSpeed.


PSC00346153
=============================
MS-Agent gives	MSAS SYSTEM ERROR in the agent log when running a ABL HTTPS
Client in the agent. 


[16/03/21@14:44:09.773-0400] P-008041 T-1086560576 1 AS-Aux MSAS Worker Thread
exiting. Number: 5, Status: 0
[16/03/21@14:46:02.732-0400] P-008041 T-1100585280 2 AS-7 AS Application Server
connected with connection id:
F7817040EC7EEF1CFFB568BEC66B358BAB9354401324.tt98. (8358)
[16/03/21@14:46:02.949-0400] P-008041 T-1100585280 1 AS-7 MSAS SYSTEM ERROR:
Attempt to assert ownership of socket 31 by 7 when owned by 7
[16/03/21@14:46:02.956-0400] P-008041 T-1087752512 1 AS-Listener MSAS Spawning
New Worker Thread. Number: 5
[16/03/21@14:46:02.956-0400] P-008041 T-1086560576 2 AS-4 AS Application Server
connected with connection id:
D229A24F567499156A42A2AD76B1E3C6A656B56E445A.tt98. (8358)
[16/03/21@14:46:02.960-0400] P-008041 T-1086560576 1 AS-4 -- (Procedure:
'server3.p' Line:1) hello world
[16/03/21@14:46:02.962-0400] P-008041 T-1086560576 2 AS-4 AS Application Server
disconnected with connection id:
D229A24F567499156A42A2AD76B1E3C6A656B56E445A.tt98. (8359)
[16/03/21@14:46:03.010-0400] P-008041 T-1086560576 2 AS-7 AS Application Server
disconnected with connection id:
F7817040EC7EEF1CFFB568BEC66B358BAB9354401324.tt98. (8359)


PSC00345163
=============================
PASOE .NET declaration fails with Could not find class or interface
System.Net.WebRequest. (12886)


PSC00343725
=============================
The JSON returned for some error conditions was invalid. This has been fixed.


PSC00342492
=============================
UnknownHostException when deploying web-app in PASOE on Linux where DNS is not
properly configured.


PSC00342308
=============================
Form Authentication is not working with PASOE when tried from Telerik App
Builder. The Java regular expression (regex) pattern used to detect a request
for JSON encoded responses in the request's accept header did not work for all
variations of clients. 
The JSON response is not being returned with the proper content type.


PSC00340093
=============================
The setenv.sh and deploySOAP.sh scripts returns an incorrect hostname if the
server has multilple IP adresses.


PSC00335049
=============================
When a .NET Open Client application connects to PASOE it fails if the .NET Open
Client has compression enabled.

Communication layer message: General Error: 9986. (7175)



p. OEBPM

PSC00349284
=============================
OEBPM: Portal server does not start (startPortalServer.sh)

The script file at <DLC_installation_path>/oebpm/jboss/bin/startPortalServer.sh
which is needed to start Portal server in daemon mode is modified to fix the
issue related to the file path.


PSC00344884
=============================
The priority field always displays Critical and is not translated in tabular
view
From the codes, the priority options are not translated but the priority value
is translated so it can't select the correct value in the drop down box.


PSC00344629
=============================
Upon submittion of the form to renew the password (login_expire.jsp), a popup
appears with the message: "Error occured while validating password".


PSC00344260
=============================
Worksteps are not translated in BPM Tabular view when using a language other
than english.


PSC00343858
=============================
We tested BPM 11.6 and found Tabular View is not working well using Internet
Exporler
 When the performer is very large, it only displayed part of the table and
there is no scollbar at the end of the table.


PSC00343506
=============================
The Password validation framework is unable to return custom validation
messages to the Portal UI.


PSC00329483
=============================
OEBPM: Headers not visible when the list of tasks enables scroll bar at portal
main page.



q. Open Client

PSC00352487
=============================
A .NET OpenClient app reads a Temp-Table with TRACKING-CHANGES turned off from
AppServer, and deletes one of its rows. It attempts to send the table with
deleted row back to AppServer and the following error is thrown:

Input result set error:  System.Data.DeletedRowInaccessibleException: Deleted
row information cannot be accessed through the row.   There might be a mismatch
between an input result set and the schema in the proxy. (7228)  (7176) 


PSC00352428
=============================
If you use the .NET DataSet or DataTable Merge() method, the resulting DataSet
or DataTable may contain duplicate ProDataTable Position or UserOrder property
values, resulting in the AppServer call failing with Protocol Error (7211) 


PSC00350863
=============================
Using .NET Dataset Merge() method to merge a dataset returned from the
AppServer with fewer fields than the original dataset back into the original
dataset will generate error 16 when the merged dataset is passed back to the
AppServer.


PSC00349983
=============================
Program which contains an output temp-table parameter where the temp-table is
defined like a database table causes an "Array index out of bounds" exception
when called from a .NET client.


PSC00349502
=============================
Memory leak in java openclient


PSC00344296
=============================
INT64 types can be incorrectly converted to RECID when passed from .NET
OpenClient to AppServer to a DATASET-HANDLE using the Dynamic API.


PSC00343771
=============================
Formula for calculating the Open Client timeout based on TcpClientRetry and
TcpClientRetryInterval appears to have changed in OpenEdge 11.6.


PSC00340561
=============================
Slow performance calling ProChangeSummary.getChanges() method of Java
OpenClient. Also, using ProDataGraph object from getChanges() call, sending the
ProDataGraph's changed rows to the Appserver using the runProc() call also
exhibits slow performance.


PSC00295914
=============================

The Java OpenClient samples GetCustOrders and UpdateDataSet Readme.txt referred
to the out-of-data sdo 1.0 jar files instead of the sdo 1.1 versions.
Therefore, when run, you will see an error on class file for
org.apache.tuscany.sdo.impl.DynamicDataObjectImpl with ProDataObject.


PSC00293753
=============================
Creating a state-free connection pool and an AppObject against that pool will
throw a "Key cannot be null" exception if CancelAllRequests() is invoked on the
AppObject instance.


PSC00287595
=============================
All Progress DLLs that go to make up the .Net OpenClient are being built with
the ANYCPU configuration.


PSC00269026
=============================
Using relative path for the -xpxgfile and -startdir parameters in the generated
runbproxygen.bat file fails with the following errors:

Proxy Generation Failed.
For details see the log file  C:\DLCWORK\test.log

The log file shows:
>>ERROR generating .NET proxies
    Default directory is not an absolute path

The same thing works in 11.2.1.



r. PDSOE

PSC00352996
=============================
When trying to import .xsd schema to ProBindingSource in PDSOE it fails with
"Selected XSD file format is bad, because "Object reference not set to an
instance of an object."" error.


PSC00352762
=============================
In OpenEdge 11.6.3 the Update Assembly References tool appears to crash when
closing in Windows 10.


PSC00351041
=============================
When adding a REST Service to a PDS project that does not contain the REST
facets, PDS should either prevent allowing the user to click OK and display a
message in the New REST Service wizard or it should display some sort of
warning on the screen.

Today one can add a new REST Service to a normal OpenEdge project (no rest
facet) and automatically open it in the REST Resource URI Editor, however no
"Defined Services" node is created and obviously no service is shown.

There are no other errors regarding the mapping or saving of the resource.


PSC00350877
=============================
PDSOE debugger doesn't stop at breakpoint


PSC00350813
=============================
When running in TTY mode for the first time in an ABLUnit project, the 'tests'
folder is removed from the PROPATH of the run configuration.

Initially the tests folder can be seen in the <project> (default PROPATH>)
segment of the PROPATH tab in the "Progress OpenEdge ABLUni"t Run Configuration
dialog.

After selecting "Use TTY for runtime" on the ABLUnit tab and running the test,
the test fails with the below content in the results.xml file.

<results.xml>

<?xml version="1.0" ?>
<testsuites errors="1" failures="0" name="ABLUnit" tests="1"><testsuite
name="C:\00365158\defect\TDD\tests\unit\pro\utils\general.cls" tests="1"
time=".093"><testcase name="test_getMessage_Cameron" status="Error"
time="0"><error>Invoke OpenEdge.ABLUnit.Reflection.ClassAnnotationInfo at line
163  (OpenEdge/ABLUnit/Reflection/ClassAnnotationInfo.r)
RunTestMethod OpenEdge.ABLUnit.Reflection.ClassAnnotationInfo at line 196 
(OpenEdge/ABLUnit/Reflection/ClassAnnotationInfo.r)
RunSelectedTestMethod OpenEdge.ABLUnit.Reflection.ClassAnnotationInfo at line
126  (OpenEdge/ABLUnit/Reflection/ClassAnnotationInfo.r)
runTestClassMethod OpenEdge.ABLUnit.Runner.ABLRunner at line 1180 
(OpenEdge/ABLUnit/Runner/ABLRunner.r)
runtests OpenEdge.ABLUnit.Runner.ABLRunner at line 1075 
(OpenEdge/ABLUnit/Runner/ABLRunner.r)
runtests OpenEdge.ABLUnit.Runner.ABLRunner at line 1013 
(OpenEdge/ABLUnit/Runner/ABLRunner.r)
runtests OpenEdge.ABLUnit.Runner.ABLRunner at line 1100 
(OpenEdge/ABLUnit/Runner/ABLRunner.r)
runtests OpenEdge.ABLUnit.Runner.ABLRunner at line 1013 
(OpenEdge/ABLUnit/Runner/ABLRunner.r)
RunTests OpenEdge.ABLUnit.Runner.ABLRunner at line 150 
(OpenEdge/ABLUnit/Runner/ABLRunner.r)
ABLUnitCore.r at line 72 
(ABLUnitCore.r)</error></testcase></testsuite></testsuites>

</results.xml>


PSC00349191
=============================
Trying to map a verb to a procedure which calls an include file to define a
temp-table and uses a compile-time argument to define the name of the
temp-table, causes the following error to be shown.

Selected file doesn't have operations.

No .pidl file is generated.


PSC00348942
=============================
PDSOE javaw.exe runs for several minutes (2-4) in the background to complete
shutdown tasks after the PDSOE UI has been closed.


PSC00348245
=============================
Internal Procedures in a .W are not shown in the PDSOE Outline View when
preprocessing is enabled.


PSC00347828
=============================
Error 'Could not create the Java Virtual Machine' occurs while installing
Progress Developer Studio for OpenEdge 32-bit on a 64-bit machine. This happens
because the system does not provide a memory of 1024m to the JVM while
launching the 'integrateArchitect.bat' program. 


PSC00346638
=============================
Loading .prof in Progress Developer Studio for OpenEdge fails with
java.lang.IllegalArgumentException: Comparison method violates its general
contract!


PSC00346128
=============================
Add trigger pop-up windows are not being displayed for objects added to a PDSOE
project created through the AppBuilder component in PDSOE. Seen in .w files
that refer to multiple objects. .log file reports NullPointerException. Instead
of Add Trigger pop-up, PDSOE goes directly to source editor.


PSC00346076
=============================
Operation does not get completed and an exception is logged in the log file
when editing a Mobile Service


PSC00345361
=============================
Using the old Procedure Editor in the Developer Studio, if a line is entered
and the Return <CR> key pressed, the last character in the line is deleted.


PSC00344930
=============================
Launching program from PDS:OE (either via Run Configuration or Launch
Configuration) causes database aliases to be created for *all* databases in the
workspaces' database profiles. 
This can cause problems if you are launching code that is supposed to set up
additional database connections and aliases at runtime.


PSC00344502
=============================
The first link in the tutorials page of the PDSOE (reference OE01-INTRO01)
redirects to wrong page.


PSC00344489
=============================
Generating ABLDocs in PDSOE for methods that have colon ':' characters in the
comments, prevents the comments being added to the generated ABLDoc.


PSC00344420
=============================
ABL Web App projects publish r-code incrementally to the PASOE server. However,
the server is unable to load the updated r-code for singleton classes and
procedures into memory. To do so requires trimming of the agents after
performing the incremental publish operation. This trimming is not taking place
as required.


PSC00343736
=============================
Error opening r-code when using flag enums with bit-wise operations.


PSC00343642
=============================
Some of the method names in the OpenEdge.Core.Assert class start with a lower
case.


PSC00343077
=============================
Using the parameter mapping process in PDSOE for REST projects, the amount of
time it takes after clicking the ... button in the REST Resource URI Editor,
for the Associate Operation With Verb page to display directly correlates with
the number of files in the project. For a project containing between 1 and
approximately 1500 files, the Associate Operation With Verb page displays
immediately after clicking the ... button of the REST Resource URI Editor. For
a project containing approximately 2600 files, the delay between clicking the
... button of the REST Resource URI Editor and the time the Associate Operation
with Verb displays is approximately 27- 30 seconds (per association). For a
project containing approximately 9500 files, the delay between clicking the ...
button of the REST Resource URI Editor and the time the Associate Operation
with Verb displays is approximately 2 minutes and 10 seconds. 

The customer describes that he was trying to add 40 additional files to an
existing project and the mapping process for all of the new files, given the
delay per file was a couple of hours.Given the cost of software engineering
personnel, this was significant.

The delay in getting the Associate Operation With Verb screen to display for a
significantly large project is too long.


PSC00342322
=============================
Poor performance and visual designer errors with many large open projects


PSC00342250
=============================
If the Windows locale isn't set to "English (United States)", the Profiler
viewer breaks completely.
The "Execution time of modules" browse fails to display data, module names
appear or disappear when clicked on etc. Other browses/grids also get corrupted
in similar fashion.


PSC00341222
=============================
After adding Telerik RadRichTextEditor control and RibbonUI there are problems 
to run a form on PDSOE


PSC00339746
=============================
PDSOE Visual Designer fails to display the underlying error message, and
instead displays a generic and unhelpful error, when a Form  (ABL class
inheriting from a .NET form) is run and an error is thrown out of the
constructor.

This does not happen if the form is created via an ABL NEW statement.


PSC00337373
=============================
If project subdirectory contains multiple ABL classes, and within those ABL
classes the package names have different casing (different mix of upper & lower
case letters), a form referring to one of those class types can no longer be
opened in Visual Designer - it will throw "Unable to resolve type information
for type" exceptions.

In addition the content assist will also show the package name multiple times -
every variant used in the .cls files will show up.


PSC00336536
=============================
The Visual Designer fails to load a derived class when there is an unrelated
folder 'com' in the project directory.	The error message is:

java.lang.reflect.InvocationTargetException: Type information for
SmartWindowForm is not available.


PSC00335526
=============================
USING statement is not automatically inserted on ctrl-space into the .cls for
variables/properties of the same type as the class itself. Class does not
compile, because of the missing USING statement. This behaviour is seen for
classes irrespective of class name. 


PSC00335119
=============================
If an error occurs in the @Before method, the test fails (as expected) and CPU
usage by Studio rises to 90+% and stays there until PDSOE is restarted. In some
machines it rises to 25% CPU usage after single run, so it means after 4 runs
it is 90+% and it remains like that until PDSOE is restarted.

If there is no error in the @Before method, CPU usage is in norm and is back to
0, when Unit test is finished.


PSC00334624
=============================
When setting new ${abldoc.title} it remains "ABLDoc Documentation" in ABLDoc
Ant. 


PSC00334612
=============================
When generating the ABLDoc documentation for the methods using the Generate
ABLDoc wizard, it does not insert multi-line comments (only first line is
inserted).


PSC00333664
=============================
Displaying inherited classes in Visual Designer causes "Visual Designer cannot
load this class" error.


PSC00333342
=============================
When working with .NET assemblies in a CHUI project in PDS, if the assemblies
directory is not the project directory, the -assemblies parameter is not
included in the resultant call to _progres.exe.

This is likely leftover from the fact that using assemblies with TTY was not
supported in the initial versions of the tool after adopting support for using
assemblies in GUI.


PSC00332594
=============================
The Outline View in PDSOE doesn't show the remaining methods after include
reference because there are inactive pre-processors in the include file.


PSC00330274
=============================
When the Visual Designer tries to load a class that inherits from a base class,
if the subclass references a property defined in the base class, then this can
result in the error:

Visual designer cannot load this class
An exception occurred loading the design canvas: The type '<supperClass.cls>'
has no property named '<property>'.


PSC00329883
=============================
If form .cls file refers to class type by package name, and the directory name
on disk changes to different casing for that package, the Visual Designer can
stop loading the form.

It'll fail with: 
"Visual Designer cannot load this class

Line <nn>: Unable to resolve type information for type <type name> for field
<member>"

The form will continue to pass syntax check & run normally.


PSC00327255
=============================
When " Maximum number of errors reported for build" to 1  is set to one and
compiling file in PDS using Build Project menu PDS doesn't stop when first
error is found and continues compiling the rest of the files in the project. 


PSC00326224
=============================
Adding a procedure library from another project in the propath leads to an
incorrect propath in PDSOE


PSC00323502
=============================
Organize USING statements ignores classes used in include files


PSC00323170
=============================
In DB Navigator, when you use the query plan to look at the SQL query there is
some strangeness if there are multiple history tabs open.  It is possible that
the SQL query plan query results will not show but if you just close all
history tabs and run it again it will work.


PSC00322358
=============================
REST Resource URI Editor displays a blank error message when the casing of a
database name in the ABL class being mapped is different than the casing of the
name in the database.


PSC00320599
=============================
USING statement Editor Assistance fails with .NET generics.  It unexpectedly
inserts the fully qualified name and fails to add the appropriate USING
statement at the top of the procedure as expected.


PSC00319609
=============================
The class cache gets out of sync in PDSOE when editing a form, which causes the
below error to be shown in the Visual Designer, even though the panel1 property
is defined in its parent class:

Line 327: Unable to locate element panel1 in type common.schedulinglocator


PSC00318538
=============================
Defining a variable that uses .NET types, which includes a coma (,) with a
blank space afterwards the list of proposals is empty. 


PSC00317618
=============================
When anything is pasted into the ScratchEditor, the cursor moves back to the
start of the editor.


PSC00314913
=============================
When saving an editor perspective as a custom perspective, the OpenEdge menu
option in the PDSOE menu stops working and then disappears from the menu.


PSC00306868
=============================
While expanding the database table list can be a bit slow (5 seconds),
expanding the fields list is extremely slow.  It appears that the more columns
there are, the longer it takes to retrieve the list.  

Expanding a given table locks up PDSOE until it finishes.  At the very least
this code should run in a separate thread and allow other processes in PDS to
function.
 
10 columns = 1.5 seconds
50 columns = 9 seconds
100 columns = 18 seconds
200 columns = 51 seconds
300 columns = 1 minute 10 seconds
400 columns = 2 minutes 20 seconds
 
This is easily reproducible with sports2000 with a number of fields added. 
Using WinSQL's catalog feature to inspect the structure of the same database
shows the table details almost instantly.


PSC00298605
=============================
The "Initializing OpenEdge Tooling" message should list the project (name)
being initialized.


PSC00286214
=============================
Missing file button combination for Logging tab in Run Configurations


PSC00269904
=============================
The ABL Scratchpad does not respect the background color used by the project
when editing code.


PSC00267870
=============================
The ABL Scratchpad does not properly handle tab characters.  Visually, it
appears that the characters are completely ignored but in fact they are there
but are treated as a zero space indentation.


PSC00260292
=============================
Return values for invoke operation missing from JSON catalog
****************************************
The invoke operations use PUT so that it can have input and output parameters.

However, although the business entity defines the return parameters for the
invoke method, this information is not added to the generated JSON catalog.
Currently the response parameters have to be added manually by editing the
invoke service in the Mobile AppBuilder.

I have quick a question regarding the invoke REST operation. This operation
uses the PUT method.
 
Why, for this specific operation do we have to map the return parameters
manually in the Mobile AppBuilder, despite the fact that the return values are
known from the class definition, at the time the JSON is generated?


PSC00228448
=============================
Architect Outline stops when procedure has &IF preprocessors
****************************************
Architect Outline stops when procedure has &IF preprocessors with do blocks



s. Porting

PSC00248179
=============================
proGetStack causes memory leak in AppServer
****************************************
Unable to unmap library files before closing them causes a memory violation as
the amount of mappings continues to grow.



t. QPE

PSC00345824
=============================
On Windows, the file %DLC%\oebuild\make\build__prchar.link file does not work
because the object file "..\obj\_prchar.exe.res" is missing. 


PSC00334557
=============================
Previously HTTP client was not installed with a 4GL Development System license.
 Now the product includes the necessary files for this functionality.


PSC00315510
=============================
The OpenEdge.core.pl file was not included in a runtime only installation.  It
is now included.



u. REST Adapter

PSC00350104
=============================
It is not possible to set RunTimeProperties like
(NsClientMinPort/NsClientMaxPort) on the OERealm Client


PSC00344678
=============================
REST documentation includes the first field in the first table definition of
the dataset


PSC00343109
=============================
The certificate store location for the AppServer SSL connection can be
specified in the appSecurity-*-oerealm.xml security templates. When specifying
a relative path for the certificate store location it does not use the web
application context as the starting point for the relative path, instead it
uses the Tomcat working directory as the starting point. This is not consistent
with the certificate store location that can be defined in the web.xml.


PSC00342367
=============================
If you set one of the properties of the user attributes within the OERealm
security template to null (blank string), then a java.lang.NullPointerException
is returned.



v. Replication

PSC00354367
=============================
RPLS cannot connect to RPLA when the Advanced Enterprise Edition RDBMS [AEE
RDBMS] is installed on one end (primary source server)
And the Enterprise Database on the other end (secondary target server) 
Error 13257 The RDBMS license on the Source machine does not match the RDBMS
license on the Target machine.


PSC00352509
=============================
OE Replication Agent fails with (10427) SYSTEM ERROR: An invalid note was
encountered by the Fathom Replication Agent in area ## at block ####, offset
##.  The error can occur duing normal replication processing.  It can also
occur when using dsrutil -C applyextent to update a Replication target database
with AI extents from the source database.


PSC00346341
=============================
DSRUTIL connects to a database that is not enabled for Replication when the
repl.properties file exists.   Immediately after making the connection, the
utility exits, without disconnecting from the database.  It is disconnected as
a dead user by the watchdog.


PSC00248634
=============================
When trying to apply the AI extents to the target database after it entered the
pre-transition state, the replication agent crashes when using the mixture of
AI extents - the archived ones and the busy one copied directly from the source
database.


PSC00245451
=============================
Replication transition failover will fail to complete if the replication
properties files are configured to use an online backup via the
backup-method=full-online argument.



w. SQL

PSC00355125
=============================
SQL client updates failing with Error in Stored Procedure Execution. (7853)


PSC00353571
=============================
Running the following SQL query against the sports2000 database:
SELECT ROWID FROM PUB.Customer WHERE customer.address2 = 2147483649; 

Gives the following error:
java.sql.SQLException: [DataDirect][OpenEdge JDBC Driver][OpenEdge] Encountered
internal error in SQL ENGINE at 1435 in
Z:/vobs_sql/sql/src/public/data/data_t.cxx. Contact Progress Technical Support


PSC00352323
=============================
Invalid number string (7498) returned by SQL query containing the following
construct: 

(numfield1<numfield2 OR numfield1 < [numeric value])


PSC00351162
=============================
When Authorized Data Truncation (ADT) is turned ON, if a multi component index
exists with a prefixed column of type VARCHAR and the query contains an
equality condition on that column, then the conditions on subsequent index
columns are ignored during index scan. 


PSC00349671
=============================
By default all the tables will have at least one index (if no index is created
by user, then default index will be present). Prior to OE 11.7, for some
reasons, if a table does not have any indexes on it, then we were reporting
error "No tuples selected by the subquery for update". Now, from OE 11.7 on
wards, in this case we return the error saying "No index found on the table
<tablename>".


PSC00349270
=============================
Performance problems with specific reports after upgrade to 11.4 due to problem
with cost estimation for subtrees in Join order optimization.


PSC00348638
=============================
A SELECT by rowid with a NOLOCK hint should not do any locking however, the
SELECT execution is in fact locking the found record.

This results in a lock failure when the selected record is locked by some other
transaction.

A SELECT by index key value, using a NOLOCk hint,  works fine, and does not
lock.


PSC00348195
=============================
Certain queries attempting to update a record via a MS SQL Server Linked Server
gets error - "[DataDirect][ODBC Progress OpenEdge Wire Protocol driver]No rows
were affected by UPDATE/DELETE WHERE CURRENT OF cursor emulation".
 
This problem has been fixed with latest drivers in 11.6.3 and above.


PSC00348139
=============================
SQL client session crashes when a UNION query has a datatype mismatch in SELECT
columns, followed by constants or parameters or scalar functions.


PSC00347153
=============================
SQL grant or revoke statements causing schema locks that seems longer than
expected for other (4GL) database users. This could occur in OpenEdge releases
prior to 11.7.0, especially for databases with a large number of Grants to
users.

Release 11.7.0 uses revised schema access approaches which result in much
quicker execution time for Revoke.

Also, Issue PSC00345833 (described in its own Release Note) provides new
locking techniques which avoid most prior schema locking problems on Grant and
on Revoke.


PSC00346844
=============================
Update All Column Statistics is extremely slow for MT tables


PSC00346741
=============================
When querying a multi-tenant database on a user table different shared-locks on
_Tenant table are created preventing to create new Tenants until the user
sessions end. 


PSC00345833
=============================
In OpenEdge version prior to 11.7.0, SQL GRANT and REVOKE operations hold
schema locks for a long time - namely as long as the transaction containing
GRANT or REVOKE.
As of OpenEdge 11.7.0, GRANT and REVOKE use a new custom schema locking
protocol that provides much higher concurrency and which locks only sql
authorization schema data.  Specifically, GRANT and REVOKE will no longer have
any impact on ABL procedures, ABL logins, or ABL execution in general.


PSC00345685
=============================
SQL login does not require password if all users created from sql side and all
changed to "sql only"


PSC00344241
=============================
SQL query fails with an internal error during join optimization.
The internal error associated with this failure is a LIKE predicate with
columns from one table on the left-hand side of the LIKE and columns from a
different table on the right-hand side of the LIKE. For example:
	       select....from......where .....	 my_table1.columnA  LIKE 
to_char( my_table2.columnB + '%')


PSC00343480
=============================
Prior to OE Release 11.6.1, OE SQL was using RC4-MD5:RC4-SHA as the default
ciphers when environment variable PSC_SSLSERVER_CIPHERS is not defined. Now, in
11.6.1, OE SQL uses default cipher suite of OpenSSL (which is used by OE SQL)
when this environment variable is not defined. Note that, default ciphers of
OpenSSL contains ciphers which are supported by TLSv1.2. 


PSC00343057
=============================
SQL: ABL interoperability failure when use default value for Timestamp with
Time Zone


PSC00342940
=============================
Revised error diagnosis in Replication Plus env - fetch failure during index
scan


PSC00334323
=============================
Some type of SQL queries that joins multiple Views containing predicates can
take a long time to complete.


PSC00333604
=============================
Database is abnormally shutdown after permission problems due to an error while
initializing user security service: insufficient context for SQL connections.


PSC00250337
=============================
Shutting down machine causes _sqlsrv2.exe crash
****************************************
Shutting down machine causes _sqlsrv2.exe crash in dsmContextGetLong


PSC00246771
=============================
Enhancement: Increased granularity with SQL logging, allowing greater control
over the information returned in a similar manner to that of ABL
client-logging.

In OpenEdge 11.7.0,  SQL now supports the ability for the DBA to control the
granularity and amount of the information logged. This can now be done by
specifying a logging level on the SET PRO_SREVER LOG statement. For example:
	   set pro_server log on  with(statement) and  with level 1
Level 1 logs the least information, level 4 logs the most.
Full documentation for the SET PRO_SERVER LOG is in the OpenEdge SQL
documentation.



x. Security

PSC00349043
=============================
When adminserver authorisation is restricted with -admingroup
The USERNAME environment variable was able to be changed to allow a
non-group-member to be able to start/stop Ubrokers.
AdminServer clients have no dependencies on insecure Java attributes going
forward so identity cannot be spoofed to claim group membership.


PSC00346086
=============================
Client can no longer connect to AppServers when the SSL_SOCKET_READ=true
environment variable is set for the client session.


PSC00345692
=============================
SSL Sockets reads are being truncated 


PSC00345585
=============================
a SSL connection failure once, all the connection to the same _mprosrv will
fail


PSC00344869
=============================
pkiutil -import The CA keys entry does not contain a valid private-key.


PSC00344081
=============================
When encountering a SOAP service providers configured with protocol TLSv1 &
cipher AES128-SHA, the default connection is failing (as OE 11.6 uses default
SSL protocol as TLSv1.2 & cipher AES128-SHA256). 

This causes the need to include the -sslSOAPProtocols and -sslSOAPCiphers
parameters in the connection, or to set the corresponding environment variables
(below).  It is also important that the protocols and ciphers to be specified
in a specific order (demonstrated in the workaround).

Environment variables:
PSC_SSLCLIENT_CIPHERS, PSC_SSLCLIENT_PROTOCOLS

The problem with having to do this is that it is anything but apparent that
this is the problem from the error messages that are returned.


PSC00344079
=============================
The root certificate "Equifax Secure Certificate Authority" is missing from the
OpenEdge certificate store.


PSC00343508
=============================
The OpenEdge products accept an invalid value for the SSL protocol and cipher.


PSC00343314
=============================
The database server does not fail to start if the default key is missing when
only using the -ssl startup parameter.


PSC00343308
=============================
The SSL debug log files (cert.server.log and cert.client.log) seem to indicate
that SSLv3 Hello messages are still being used by both ABL and SQL clients when
communicating with an OpenEdge database.


PSC00339142
=============================
Unknown ssl error (9318) connecting to an SNI enabled web service


PSC00339091
=============================
Sometimes an HTTPS response contains an empty or truncated content when using
the HTTP client.



y. Server Technology

PSC00354126
=============================
.NET application reports the following error after migrating from OE 11.3.3 to
11.6.3:

'None of the discovered or specified addresses match the socket address
family.'


PSC00351993
=============================
Running Open Client applications using proxygen that use Progress.o4glrt.dll,
application can produce error "A first chance exception of type
'System.IndexOutOfRangeException' occurred in Progress.o4glrt.dll.


PSC00350295
=============================
When using CANCEL-REQUESTS to cancel outstanding ASYNC requests while connected
to the AppServer via Aia, the session ignores the cancel.


PSC00344432
=============================
The 'certutil -list' command returns a warning for each certificate on UNIX /
Linux.


PSC00344401
=============================
The OpenEdge 11.6 default installation contains a hash file (88dc3f02-sha384.0)
in $DLC\certs which causes the 'certutil -list' command to fail.


PSC00342046
=============================
Java OOM memory leak with WSA and or REST monitoring enabled.


PSC00246119
=============================
If DLC is set as one of the system variables, install hangs



z. TTY

PSC00341756
=============================
In the Progress Character Client environment the DISABLE ALL statement does not
work properly for fill-in fields enabled with TEXT option if the READ-ONLY
property is set to TRUE. 



{. UI Controls

PSC00348174
=============================
The file Infragistics4.Win.UltraWinCalcManager.v15.1.FormulaBuilder.dll is not
in the %DLC%\bin\infragistics\winforms directory.
It is required for WinCalcManager to work.
The WinCalcManager FormulaBuilder can be shown to the user at run-time.



|. Web Services Adapter

PSC00349746
=============================
OEM pings to remote WSA appear to have a very long timeout


PSC00346055
=============================
Error while trying to import a V10.2B .wsd file into a V11 WSA with message:
failed to parse the WSD file entered: <filename.wsd>. File doesn't contain
valid WSD content. Import cannot proceed.
Getting import info failed.



}. WebClient

PSC00341402
=============================
WebClient crashes when updating an application running on Windows 10
touchscreen devices.



~. WebSpeed

PSC00351297
=============================
Uploading a file from the browser to WebSpeed and modifying the file name or
path allows the upload directory to be escaped.


PSC00345530
=============================
Segmentation Fault when connecting SSL enabled Webspeed Messenger to Webspeed
broker.


PSC00344804
=============================
wtbman -q -all command to list status of resources failed with error.

 Error Message: No entries for personality WS (8300)


PSC00344613
=============================
Validating cause for error 9257


PSC00339570
=============================
Messenger error 6369 when Webspeed service name exceeds 24 characters


PSC00337475
=============================
wsisa.dll messenger fails when setting loggingLevel > 0



---------------------------------------
Third Party Acknowledgments
---------------------------------------

Release Notes - Documentation Third Party Acknowledgements

Copyright (c) 1984-2017 Progress Software Corporation and/or one of its 
subsidiaries or affiliates. All rights reserved.

One or more products in the Progress OpenEdge v11.7 release includes 
third party components covered by licenses that require that the 
following documentation notices be provided.  If changes in third party 
components occurred for the current release of the Product, the 
following Third Party Acknowledgements may contain notice updates to 
any earlier versions provided in documentation or README file. 

Progress OpenEdge v11.7 may incorporate ANT v1.5.4. Such technology is 
subject to the following terms and conditions: The Apache Software 
License, Version 1.1, applies to all versions of up to ant 1.6.0 
included. The Apache Software License, Version 1.1 - Copyright (C) 
2000-2003 The Apache Software Foundation. All rights reserved. 
Redistribution and use in source and binary forms, with or without 
modification, are permitted provided that the following conditions are 
met: 1. Redistributions of source code must retain the above copyright 
notice, this list of conditions and the following disclaimer. 2. 
Redistributions in binary form must reproduce the above copyright 
notice, this list of conditions and the following disclaimer in the 
documentation and/or other materials provided with the distribution.  
3. The end-user documentation included with the redistribution, if any, 
must include the following acknowledgment:  "This product includes 
software developed by the Apache Software Foundation 
(http://www.apache.org/)." Alternately, this acknowledgment may appear 
in the software itself, if and wherever such third-party 
acknowledgments normally appear. 4. The names "Ant" and "Apache 
Software Foundation" must not be used to endorse or promote products 
derived from this software without prior written permission. For 
written permission, please contact apache@apache.org.  5. Products 
derived from this software may not be called "Apache", nor may "Apache" 
appear in their name, without prior written permission of the Apache 
Software Foundation. THIS SOFTWARE IS PROVIDED ``AS IS'' AND ANY 
EXPRESSED OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE 
IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS  FOR A PARTICULAR  
PURPOSE ARE  DISCLAIMED.  IN NO EVENT SHALL THE APACHE SOFTWARE 
FOUNDATION OR ITS CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, 
INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, 
BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS 
OF USE, DATA, OR  PROFITS; OR BUSINESS  INTERRUPTION)  HOWEVER CAUSED 
AND ON ANY  THEORY OF LIABILITY,  WHETHER  IN CONTRACT,  STRICT 
LIABILITY,  OR TORT (INCLUDING  NEGLIGENCE OR  OTHERWISE) ARISING IN  
ANY WAY OUT OF THE  USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE 
POSSIBILITY OF SUCH DAMAGE. This software consists of voluntary 
contributions made by many individuals on behalf of the Apache Software 
Foundation.  For more information on the Apache Software Foundation, 
please see <http://www.apache.org/>.

Progress OpenEdge v11.7 may incorporate Apache Avalon 2002/08/06, 
Apache Batik v1.1, Apache Jakarta POI v1.1, Apache POI 2003/05/14, 
Regexp (Pure Java Regular Expression) v1.2, and XMLTask v1.15.1  from 
The Apache Software Foundation.  Such technologies are subject to the 
following terms and conditions:  The Apache Software License, Version 
1.1 Copyright (c) 2000 The Apache Software Foundation.  All rights 
reserved. Redistribution and use in source and binary forms, with or 
without modification, are permitted provided that the following 
conditions are met: 1. Redistributions of source code must retain the 
above copyright notice, this list of conditions and the following 
disclaimer. 2. Redistributions in binary form must reproduce the above 
copyright notice, this list of conditions and the following disclaimer 
in the documentation and/or other materials provided with the 
distribution. 3. The end-user documentation included with the 
redistribution, if any, must include the following acknowledgment: 
"This product includes software developed by the Apache Software 
Foundation (http://www.apache.org/)." Alternately, this acknowledgment 
may appear in the software itself, if and wherever such third-party 
acknowledgments normally appear. 4. The names "Apache" and "Apache 
Software Foundation" must not be used to endorse or promote products 
derived from this software without prior written permission. For 
written permission, please contact apache@apache.org. 5. Products 
derived from this software may not be called "Apache", nor may "Apache" 
appear in their name, without prior written permission of the Apache 
Software Foundation. THIS SOFTWARE IS PROVIDED ``AS IS'' AND ANY 
EXPRESSED OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE 
IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR 
PURPOSE ARE DISCLAIMED.  IN NO EVENT SHALL THE APACHE SOFTWARE 
FOUNDATION OR ITS CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, 
INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, 
BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS 
OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND 
ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR 
TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE 
USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH 
DAMAGE. This software consists of voluntary contributions made by many 
individuals on behalf of the Apache Software Foundation.  For more 
information on the Apache Software Foundation, please see 
<http://www.apache.org/>. Portions of this software are based upon 
public domain software originally written at the National Center for 
Supercomputing Applications, University of Illinois, Urbana-Champaign.

Progress OpenEdge v11.7 may incorporate Apache JAXP v1.3.04 from The 
Apache Software Foundation.  Such technology is subject to the 
following terms and conditions:  The Apache Software License, Version 
1.1 Copyright (c) 1999-2003 The Apache Software Foundation.  All rights 
reserved.  Redistribution and use in source and binary forms, with or 
without modification, are permitted provided that the following 
conditions are met: 1. Redistributions of source code must retain the 
above copyright notice, this list of conditions and the following 
disclaimer. 2. Redistributions in binary form must reproduce the above 
copyright notice, this list of conditions and the following disclaimer 
in the documentation and/or other materials provided with the 
distribution. 3. The end-user documentation included with the 
redistribution, if any, must include the following acknowledgment:  
"This product includes software developed by the Apache Software 
Foundation (http://www.apache.org/)." Alternately, this acknowledgment 
may appear in the software itself, if and wherever such third-party 
acknowledgments normally appear. 4. The names "Xalan" and "Apache 
Software Foundation" must not be used to endorse or promote products 
derived from this software without prior written permission. For 
written permission, please contact apache@apache.org. 5. Products 
derived from this software may not be called "Apache", nor may "Apache" 
appear in their name, without prior written   permission of the Apache 
Software Foundation.  THIS SOFTWARE IS PROVIDED ``AS IS'' AND ANY 
EXPRESSED OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE 
IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR 
PURPOSE ARE DISCLAIMED.  IN NO EVENT SHALL THE APACHE SOFTWARE 
FOUNDATION OR ITS CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, 
INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, 
BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS 
OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND 
ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR 
TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE 
USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH 
DAMAGE.  This software consists of voluntary contributions made by many 
individuals on behalf of the Apache Software Foundation and was 
originally based on software copyright (c) 1999, Lotus Development 
Corporation., http://www.lotus.com.  For more information on the Apache 
Software Foundation, please see http://www.apache.org/.

Progress OpenEdge v11.7 may incorporate Crimson v1.1.3 (as part of 1PDT 
Progress Extensions for Eclipse (PEX) v2.6.0). Such technology is 
subject to the following terms and conditions:  The Apache Software 
License, Version 1.1 Copyright (c) 1999-2003 The Apache Software 
Foundation.  All rights reserved.  Redistribution and use in source and 
binary forms, with or without modification, are permitted provided that 
the following conditions are met:  1. Redistributions of source code 
must retain the above copyright notice, this list of conditions and the 
following disclaimer.  2. Redistributions in binary form must reproduce 
the above copyright notice, this list of conditions and the following 
disclaimer in the documentation and/or other materials provided with 
the distribution. 3. The end-user documentation included with the 
redistribution, if any, must include the following acknowledgment: 
"This product includes software developed by the Apache Software 
Foundation (http://www.apache.org/)." Alternately, this acknowledgment 
may appear in the software itself, if and wherever such third-party 
acknowledgments normally appear. 4. The names "Xerces" and "Apache 
Software Foundation" must not be used to endorse or promote products 
derived from this software without prior written permission. For 
written permission, please contact apache@apache.org. 5. Products 
derived from this software may not be called "Apache", nor may "Apache" 
appear in their name, without prior written permission of the Apache 
Software Foundation. THIS SOFTWARE IS PROVIDED ``AS IS'' AND ANY 
EXPRESSED OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE 
IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR 
PURPOSE ARE DISCLAIMED.  IN NO EVENT SHALL THE APACHE SOFTWARE 
FOUNDATION OR ITS CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, 
INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, 
BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS 
OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND 
ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR 
TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE 
USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH 
DAMAGE. This software consists of voluntary contributions made by many 
individuals on behalf of the Apache Software Foundation and was 
originally based on software copyright (c) 1999, International Business 
Machines, Inc., http://www.ibm.com.  For more information on the Apache 
Software Foundation, please see <http://www.apache.org/>.

Progress OpenEdge v11.7 may incorporate jfor v0.7.2 from jfor.  Such 
technology is subject to the following terms and conditions: jfor 
Apache-Style Software License. Copyright (c) 2002 by the jfor project. 
All rights reserved. Redistribution and use in source and binary forms, 
with or without modification, are permitted provided that the following 
conditions are met: 1. Redistributions of source code must retain the 
above copyright notice, this list of conditions and the following 
disclaimer.  2. Redistributions in binary form must reproduce the above 
copyright notice, this list of conditions and the following disclaimer 
in the documentation and/or other materials provided with the 
distribution. 3. The end-user documentation included with the 
redistribution, if any, must include the following acknowledgment:  
"This product includes software developed by the jfor project 
(http://www.jfor.org)." Alternately, this acknowledgment may appear in 
the software itself, if and wherever such third-party acknowledgments 
normally appear. 4. The name "jfor" must not be used to endorse or 
promote products derived from this software without prior written 
permission.  For written permission, please contact info@jfor.org. 5. 
Products derived from this software may not be called "jfor", nor may 
"jfor" appear in their name, without prior written permission of 
info@jfor.org. THIS SOFTWARE IS PROVIDED ``AS IS'' AND ANY EXPRESSED OR 
IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED 
WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE 
DISCLAIMED.  IN NO EVENT SHALL THE JFOR PROJECT OR ITS CONTRIBUTORS BE 
LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR 
CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF 
SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR 
BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, 
WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR 
OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE,  EVEN IF 
ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

Progress OpenEdge v11.7 may incorporate Apache LogKit v1.2 from The 
Apache Software Foundation.  Such technologies are subject to the 
following terms and conditions:  The Apache Software License, Version 
1.1 -- Copyright (C) 1997-2003 The Apache Software Foundation. All 
rights reserved. Redistribution and use in source and binary forms, 
with or without modification, are permitted provided that the following 
conditions are met: 1. Redistributions of source code must retain the 
above copyright notice, this list of conditions and the following 
disclaimer. 2. Redistributions in binary form must reproduce the above 
copyright notice, this list of conditions and the following disclaimer 
in the documentation and/or other materials provided with the 
distribution. 3. The end-user documentation included with the 
redistribution, if any, must include the following acknowledgment:  
"This product includes software developed by the Apache Software 
Foundation (http://www.apache.org/)." Alternately, this acknowledgment 
may appear in the software itself, if and wherever such third-party 
acknowledgments normally appear. 4. The names "LogKit", "Jakarta" and 
"Apache Software Foundation" must not be used to endorse or promote 
products derived from this software without prior written permission. 
For written permission, please contact apache@apache.org. 5. Products 
derived from this software may not be called "Apache", nor may "Apache" 
appear in their name, without prior written permission of the Apache 
Software Foundation. THIS SOFTWARE IS PROVIDED ``AS IS'' AND ANY 
EXPRESSED OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE 
IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS  FOR A PARTICULAR  
PURPOSE ARE  DISCLAIMED. IN NO EVENT SHALL THE APACHE SOFTWARE  
FOUNDATION  OR ITS CONTRIBUTORS  BE LIABLE FOR ANY DIRECT, INDIRECT, 
INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,BUT 
NOT LIMITED TO, PROCUREMENT  OF SUBSTITUTE GOODS OR SERVICES; LOSS OF 
USE, DATA, OR PROFITS; OR BUSINESS  INTERRUPTION)  HOWEVER CAUSED AND 
ON ANY THEORY OF LIABILITY, WHETHER  IN CONTRACT,  STRICT LIABILITY, OR 
TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE 
USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH 
DAMAGE. This software consists of voluntary contributions made by many 
individuals on behalf of the Apache Software Foundation. For more 
information on the Apache Software Foundation, please see 
<http://www.apache.org/>.

Progress OpenEdge v11.7 may incorporate Xerces for Java XML Parser 
v2.6.2. Such technology is subject to the following terms and 
conditions: The Apache Software License, Version 1.1 Copyright (c) 1999 
The Apache Software Foundation.  All rights reserved. Redistribution 
and use in source and binary forms, with or without modification, are 
permitted provided that the following conditions are met: 1. 
Redistributions of source code must retain the above copyright notice, 
this list of conditions and the following disclaimer. 2. 
Redistributions in binary form must reproduce the above copyright 
notice, this list of conditions and the following disclaimer in the 
documentation and/or other materials provided with the distribution. 3. 
The end-user documentation included with the redistribution, if any, 
must include the following acknowledgment: "This product includes 
software developed by the Apache Software Foundation 
(http://www.apache.org/)." Alternately, this acknowledgment may appear 
in the software itself, if and wherever such third-party 
acknowledgments normally appear. 4. The names "Xerces" and "Apache 
Software Foundation" must not be used to endorse or promote products 
derived from this software without prior written permission. For 
written permission, please contact apache@apache.org. 5. Products 
derived from this software may not be called "Apache", nor may "Apache" 
appear in their name, without prior written permission of the Apache 
Software Foundation. THIS SOFTWARE IS PROVIDED ``AS IS'' AND ANY 
EXPRESSED OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE 
IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR 
PURPOSE ARE DISCLAIMED.  IN NO EVENT SHALL THE APACHE SOFTWARE 
FOUNDATION OR ITS CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, 
INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, 
BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS 
OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND 
ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR 
TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE 
USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH 
DAMAGE. This software consists of voluntary contributions made by many 
individuals on behalf of the Apache Software Foundation and was 
originally based on software copyright (c) 1999, International Business 
Machines, Inc., http://www.ibm.com.  For more information on the Apache 
Software Foundation, please see <http://www.apache.org/>.

Progress OpenEdge v11.7 may incorporate SOAP v2.3.1 from Apache 
Foundation. Such technology is subject to the following terms and 
conditions: The Apache Software License, Version 1.1
Copyright (c) 1999 The Apache Software Foundation.  All rights 
reserved.
Redistribution and use in source and binary forms, with or without 
modification, are permitted provided that the following conditions are 
met:
1. Redistributions of source code must retain the above copyright 
notice, this list of conditions and the following disclaimer. 
2. Redistributions in binary form must reproduce the above copyright 
notice, this list of conditions and the following disclaimer in the 
documentation and/or other materials provided with the distribution.
3. The end-user documentation included with the redistribution, if any, 
must include the following acknowledgment: "This product includes 
software developed by the Apache Software Foundation 
(http://www.apache.org/)."
Alternately, this acknowledgment may appear in the software itself, if 
and wherever such third-party acknowledgments normally appear.
4. The names "SOAP" and "Apache Software Foundation" must not be used 
to endorse or promote products derived from this software without prior 
written permission. For written permission, please contact 
apache@apache.org.
5. Products derived from this software may not be called "Apache", nor 
may "Apache" appear in their name, without prior written permission of 
the Apache Software Foundation.
THIS SOFTWARE IS PROVIDED ``AS IS'' AND ANY EXPRESSED OR IMPLIED 
WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF 
MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED.  
IN NO EVENT SHALL THE APACHE SOFTWARE FOUNDATION OR ITS CONTRIBUTORS BE 
LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR 
CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF 
SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR 
BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, 
WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR 
OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF 
ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
This software consists of voluntary contributions made by many 
individuals on behalf of the Apache Software Foundation. For more 
information on the Apache Software Foundation, please see 
<http://www.apache.org/>.

Progress OpenEdge v11.7 may incorporate ANTLR (Another Tool for 
Language Recognition) v2.7.6. Such technology is subject to the 
following terms and conditions: ANTLR 3 License [The BSD License] 
Copyright (c) 2003-2006, Terence Parr All rights reserved.  
Redistribution and use in source and binary forms, with or without 
modification, are permitted provided that the following conditions are 
met:  Redistributions of source code must retain the above copyright 
notice, this list of conditions and the following disclaimer.  
Redistributions in binary form must reproduce the above copyright 
notice, this list of conditions and the following disclaimer in the 
documentation and/or other materials provided with the distribution. 
Neither the name of the author nor the names of its contributors may be 
used to endorse or promote products derived from this software without 
specific prior written permission. THIS SOFTWARE IS PROVIDED BY THE 
COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED 
WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF 
MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN 
NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY 
DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL 
DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS 
OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) 
HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, 
STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING 
IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE 
POSSIBILITY OF SUCH DAMAGE.

Progress OpenEdge v11.7 may incorporate Castor v0.9.3 from castor.org.  
Such technology is subject to the following terms and conditions: 
Original Intalio license - Copyright 1999-2004 (C) Intalio Inc., and 
others. All Rights Reserved. Redistribution and use of this software 
and associated documentation ("Software"), with or without 
modification, are permitted provided that the following conditions are 
met: 1. Redistributions of source code must retain copyright statements 
and notices. Redistributions must also contain a copy of this document. 
2. Redistributions in   binary form must reproduce the above copyright 
notice, this list of conditions and the following disclaimer in the 
documentation and/or other materials provided with the distribution. 3. 
The name "ExoLab" must not be used to endorse    or promote products 
derived from this Software without prior written permission of Intalio 
Inc. For written permission, please contact info@exolab.org. 4. 
Products derived from this Software may not be called "Castor" nor may 
"Castor" appear in their names without prior written permission of 
Intalio Inc. Exolab, Castor and Intalio are trademarks of Intalio Inc. 
5. Due credit should be given to the ExoLab Project 
(http://www.exolab.org/). THIS SOFTWARE IS PROVIDED BY INTALIO AND 
CONTRIBUTORS ``AS IS'' AND ANY EXPRESSED OR IMPLIED WARRANTIES, 
INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF 
MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN 
NO EVENT SHALL INTALIO OR ITS CONTRIBUTORS BE LIABLE FOR ANY DIRECT, 
INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES 
(INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR 
SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) 
HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, 
STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING 
IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE 
POSSIBILITY OF SUCH DAMAGE.

Progress OpenEdge v11.7 may incorporate Decimal Conversion Code 
(dtoa.c; g_fmt.c; rnd_prod.s; decstrod.c; decstrtof.c; dmisc.c; 
gdtoa.c; gdtoa.h; gdtoaimp.h; gethex.c; gmisc.c; hd_init.c; misc.c; 
smisc.c; strtodg.c; strtord.c; sum.c; ulp.c). Such technologies are 
subject to the following terms and conditions: dtoa.c License: The 
author of this software is David M. Gay. Copyright (c) 1991, 2000, 2001 
by Lucent Technologies. Permission to use, copy, modify, and distribute 
this software for any purpose without fee is hereby granted, provided 
that this entire notice is included in all copies of any software which 
is or includes a copy or modification of this software and in all 
copies of the supporting documentation for such software. THIS SOFTWARE 
IS BEING PROVIDED "AS IS", WITHOUT ANY EXPRESS OR IMPLIED WARRANTY.  IN 
PARTICULAR, NEITHER THE AUTHOR NOR LUCENT MAKES ANY REPRESENTATION OR 
WARRANTY OF ANY KIND CONCERNING THE MERCHANTABILITY OF THIS SOFTWARE OR 
ITS FITNESS FOR ANY PARTICULAR PURPOSE. g_fmt.c License: The author of 
this software is David M. Gay. Copyright (c) 1991, 1996 by Lucent 
Technologies. Permission to use, copy, modify, and distribute this 
software for any purpose without fee is hereby granted, provided that 
this entire notice is included in all copies of any software which is 
or includes a copy or modification of this software and in all copies 
of the supporting documentation for such software.  THIS SOFTWARE IS 
BEING PROVIDED "AS IS", WITHOUT ANY EXPRESS OR IMPLIED WARRANTY.  IN 
PARTICULAR, NEITHER THE AUTHOR NOR LUCENT MAKES ANY REPRESENTATION OR 
WARRANTY OF ANY KIND CONCERNING THE MERCHANTABILITY OF THIS SOFTWARE OR 
ITS FITNESS FOR ANY PARTICULAR PURPOSE. 
rnd_prod.s License: The author of this software is David M. Gay. 
Copyright (c) 1991 by Lucent Technologies. Permission to use, copy, 
modify, and distribute this software for any purpose without fee is 
hereby granted, provided that this entire notice is included in all 
copies of any software which is or includes a copy or modification of 
this software and in all copies of the supporting documentation for 
such software. THIS SOFTWARE IS BEING PROVIDED "AS IS", WITHOUT ANY 
EXPRESS OR IMPLIED WARRANTY.  IN PARTICULAR, NEITHER THE AUTHOR NOR 
LUCENT MAKES ANY REPRESENTATION OR WARRANTY OF ANY KIND CONCERNING THE 
MERCHANTABILITY OF THIS SOFTWARE OR ITS FITNESS FOR ANY PARTICULAR 
PURPOSE. decstrtod.c License: The author of this software is David M. 
Gay. Copyright (C) 1998-2001 by Lucent Technologies All Rights Reserved 
Permission to use, copy, modify, and distribute this software and its 
documentation for any purpose and without fee is hereby granted, 
provided that the above copyright notice appear in all copies and that 
both that the copyright notice and this permission notice and warranty 
disclaimer appear in supporting documentation, and that the name of 
Lucent or any of its entities not be used in advertising or publicity 
pertaining to distribution of the software without specific, written 
prior permission. LUCENT DISCLAIMS ALL WARRANTIES WITH REGARD TO THIS 
SOFTWARE, INCLUDING ALL IMPLIED WARRANTIES OF MERCHANTABILITY AND 
FITNESS. IN NO EVENT SHALL LUCENT OR ANY OF ITS ENTITIES BE LIABLE FOR 
ANY SPECIAL, INDIRECT OR CONSEQUENTIAL DAMAGES OR ANY DAMAGES 
WHATSOEVER RESULTING FROM LOSS OF USE, DATA OR PROFITS, WHETHER IN AN 
ACTION OF CONTRACT, NEGLIGENCE OR OTHER TORTIOUS ACTION, ARISING OUT OF 
OR IN CONNECTION WITH THE USE OR PERFORMANCE OF THIS SOFTWARE. 
decstrtof.c License: The author of this software is David M. Gay. 
Copyright (C) 1998, 2000 by Lucent Technologies All Rights Reserved 
Permission to use, copy, modify, and distribute this software and its 
documentation for any purpose and without fee is hereby
granted, provided that the above copyright notice appear in all copies 
and that both that the copyright notice and this permission notice and 
warranty disclaimer appear in supporting documentation, and that the 
name of Lucent or any of its entities not be used in advertising or 
publicity pertaining to distribution of the software without specific, 
written prior permission. LUCENT DISCLAIMS ALL WARRANTIES WITH REGARD 
TO THIS SOFTWARE, INCLUDING ALL IMPLIED WARRANTIES OF MERCHANTABILITY 
AND FITNESS. IN NO EVENT SHALL LUCENT OR ANY OF ITS ENTITIES BE LIABLE 
FOR ANY SPECIAL, INDIRECT OR CONSEQUENTIAL DAMAGES OR ANY DAMAGES 
WHATSOEVER RESULTING FROM LOSS OF USE, DATA OR PROFITS, WHETHER IN AN 
ACTION OF CONTRACT, NEGLIGENCE OR OTHER TORTIOUS ACTION, ARISING OUT OF 
OR IN CONNECTION WITH THE USE OR PERFORMANCE OF THIS SOFTWARE. dmisc.c 
License: The author of this software is David M. Gay. Copyright (C) 
1998 by Lucent Technologies All Rights Reserved Permission to use, 
copy, modify, and distribute this software and its documentation for 
any purpose and without fee is hereby granted, provided that the above 
copyright notice appear in all copies and that both that the copyright 
notice and this permission notice and warranty disclaimer appear in 
supporting documentation, and that the name of Lucent or any of its 
entities not be used in advertising or publicity pertaining to 
distribution of the software without specific, written prior 
permission. LUCENT DISCLAIMS ALL WARRANTIES WITH REGARD TO THIS 
SOFTWARE, INCLUDING ALL IMPLIED WARRANTIES OF MERCHANTABILITY AND 
FITNESS. IN NO EVENT SHALL LUCENT OR ANY OF ITS ENTITIES BE LIABLE FOR 
ANY SPECIAL, INDIRECT OR CONSEQUENTIAL DAMAGES OR ANY DAMAGES 
WHATSOEVER RESULTING FROM LOSS OF USE, DATA OR PROFITS, WHETHER IN AN 
ACTION OF CONTRACT, NEGLIGENCE OR OTHER TORTIOUS ACTION, ARISING OUT OF 
OR IN CONNECTION WITH THE USE OR PERFORMANCE OF THIS SOFTWARE. gdtoa.c 
License: The author of this software is David M. Gay. Copyright (C) 
1998, 1999 by Lucent Technologies All Rights Reserved Permission to 
use, copy, modify, and distribute this software and its documentation 
for any purpose and without fee is hereby granted, provided that the 
above copyright notice appear in all copies and that both that the 
copyright notice and this permission notice and warranty disclaimer 
appear in supporting documentation, and that the name of Lucent or any 
of its entities not be used in advertising or publicity pertaining to 
distribution of the software without specific, written prior 
permission. LUCENT DISCLAIMS ALL WARRANTIES WITH REGARD TO THIS 
SOFTWARE, INCLUDING ALL IMPLIED WARRANTIES OF MERCHANTABILITY AND 
FITNESS. IN NO EVENT SHALL LUCENT OR ANY OF ITS ENTITIES BE LIABLE FOR 
ANY SPECIAL, INDIRECT OR CONSEQUENTIAL DAMAGES OR ANY DAMAGES 
WHATSOEVER RESULTING FROM LOSS OF USE, DATA OR PROFITS, WHETHER IN AN 
ACTION OF CONTRACT, NEGLIGENCE OR OTHER TORTIOUS ACTION, ARISING OUT OF 
OR IN CONNECTION WITH THE USE OR PERFORMANCE OF THIS SOFTWARE. gdtoa.h 
License: The author of this software is David M. Gay. Copyright (C) 
1998 by Lucent Technologies All Rights Reserved Permission to use, 
copy, modify, and distribute this software and its documentation for 
any purpose and without fee is hereby
granted, provided that the above copyright notice appear in all copies 
and that both that the copyright notice and this permission notice and 
warranty disclaimer appear in supporting documentation, and that the 
name of Lucent or any of its entities not be used in advertising or 
publicity pertaining to distribution of the software without specific, 
written prior permission. LUCENT DISCLAIMS ALL WARRANTIES WITH REGARD 
TO THIS SOFTWARE, INCLUDING ALL IMPLIED WARRANTIES OF MERCHANTABILITY 
AND FITNESS. IN NO EVENT SHALL LUCENT OR ANY OF ITS ENTITIES BE LIABLE 
FOR ANY SPECIAL, INDIRECT OR CONSEQUENTIAL DAMAGES OR ANY DAMAGES 
WHATSOEVER RESULTING FROM LOSS OF USE, DATA OR PROFITS, WHETHER IN AN 
ACTION OF CONTRACT, NEGLIGENCE OR OTHER TORTIOUS ACTION, ARISING OUT OF 
OR IN CONNECTION WITH THE USE OR PERFORMANCE OF THIS SOFTWARE. 
gdtoaimp.h License: The author of this software is David M. Gay. 
Copyright (C) 1998-2000 by Lucent Technologies All Rights Reserved 
Permission to use, copy, modify, and distribute this software and its 
documentation for any purpose and without fee is hereby granted, 
provided that the above copyright notice appear in all copies and that 
both that the copyright notice and this permission notice and warranty 
disclaimer appear in supporting documentation, and that the name of 
Lucent or any of its entities not be used in advertising or publicity 
pertaining to distribution of the software without specific, written 
prior permission. LUCENT DISCLAIMS ALL WARRANTIES WITH REGARD TO THIS 
SOFTWARE, INCLUDING ALL IMPLIED WARRANTIES OF MERCHANTABILITY AND 
FITNESS. IN NO EVENT SHALL LUCENT OR ANY OF ITS ENTITIES BE LIABLE FOR 
ANY SPECIAL, INDIRECT OR CONSEQUENTIAL DAMAGES OR ANY DAMAGES 
WHATSOEVER RESULTING FROM LOSS OF USE, DATA OR PROFITS, WHETHER IN AN 
ACTION OF CONTRACT, NEGLIGENCE OR OTHER TORTIOUS ACTION, ARISING OUT OF 
OR IN CONNECTION WITH THE USE OR PERFORMANCE OF THIS SOFTWARE. gethex.c 
License: The author of this software is David M. Gay. Copyright (C) 
1998 by Lucent Technologies All Rights Reserved Permission to use, 
copy, modify, and distribute this software and its documentation for 
any purpose and without fee is hereby granted, provided that the above 
copyright notice appear in all copies and that both that the copyright 
notice and this permission notice and warranty disclaimer appear in 
supporting documentation, and that the name of Lucent or any of its 
entities not be used in advertising or publicity pertaining to 
distribution of the software without specific, written prior 
permission. LUCENT DISCLAIMS ALL WARRANTIES WITH REGARD TO THIS 
SOFTWARE, INCLUDING ALL IMPLIED WARRANTIES OF MERCHANTABILITY AND 
FITNESS. IN NO EVENT SHALL LUCENT OR ANY OF ITS ENTITIES BE LIABLE FOR 
ANY SPECIAL, INDIRECT OR CONSEQUENTIAL DAMAGES OR ANY DAMAGES 
WHATSOEVER RESULTING FROM LOSS OF USE, DATA OR PROFITS, WHETHER IN AN 
ACTION OF CONTRACT, NEGLIGENCE OR OTHER TORTIOUS ACTION, ARISING OUT OF 
OR IN CONNECTION WITH THE USE OR PERFORMANCE OF THIS SOFTWARE. gmisc.c 
License: The author of this software is David M. Gay. Copyright (C) 
1998 by Lucent Technologies All Rights Reserved Permission to use, 
copy, modify, and distribute this software and its documentation for 
any purpose and without fee is hereby granted, provided that the above 
copyright notice appear in all copies and that both that the copyright 
notice and this permission notice and warranty disclaimer appear in 
supporting documentation, and that the name of Lucent or any of its 
entities not be used in advertising or publicity pertaining to 
distribution of the software without specific, written prior 
permission. LUCENT DISCLAIMS ALL WARRANTIES WITH REGARD TO THIS 
SOFTWARE, INCLUDING ALL IMPLIED WARRANTIES OF MERCHANTABILITY AND 
FITNESS. IN NO EVENT SHALL LUCENT OR ANY OF ITS ENTITIES BE LIABLE FOR 
ANY SPECIAL, INDIRECT OR CONSEQUENTIAL DAMAGES OR ANY DAMAGES 
WHATSOEVER RESULTING FROM LOSS OF USE, DATA OR PROFITS, WHETHER IN AN 
ACTION OF CONTRACT, NEGLIGENCE OR OTHER TORTIOUS ACTION, ARISING OUT OF 
OR IN CONNECTION WITH THE USE OR PERFORMANCE OF THIS SOFTWARE. 
hd_init.c License: The author of this software is David M. Gay. 
Copyright (C) 2000 by Lucent Technologies All Rights Reserved 
Permission to use, copy, modify, and distribute this software and its 
documentation for any purpose and without fee is hereby granted, 
provided that the above copyright notice appear in all copies and that 
both that the copyright notice and this permission notice and warranty 
disclaimer appear in supporting documentation, and that the name of 
Lucent or any of its entities not be used in advertising or publicity 
pertaining to distribution of the software without specific, written 
prior permission. LUCENT DISCLAIMS ALL WARRANTIES WITH REGARD TO THIS 
SOFTWARE, INCLUDING ALL IMPLIED WARRANTIES OF MERCHANTABILITY AND 
FITNESS. IN NO EVENT SHALL LUCENT OR ANY OF ITS ENTITIES BE LIABLE FOR 
ANY SPECIAL, INDIRECT OR CONSEQUENTIAL DAMAGES OR ANY DAMAGES 
WHATSOEVER RESULTING FROM LOSS OF USE, DATA OR PROFITS, WHETHER IN AN 
ACTION OF CONTRACT, NEGLIGENCE OR OTHER TORTIOUS ACTION, ARISING OUT OF 
OR IN CONNECTION WITH THE USE OR PERFORMANCE OF THIS SOFTWARE. misc.c 
License: The author of this software is David M. Gay. Copyright (C) 
1998, 1999 by Lucent Technologies All Rights Reserved Permission to 
use, copy, modify, and distribute this software and its documentation 
for any purpose and without fee is hereby granted, provided that the 
above copyright notice appear in all copies and that both that the 
copyright notice and this permission notice and warranty  disclaimer 
appear in supporting documentation, and that the name of Lucent or any 
of its entities not be used in advertising or publicity pertaining to 
distribution of the software without specific, written prior 
permission. LUCENT DISCLAIMS ALL WARRANTIES WITH REGARD TO THIS 
SOFTWARE, INCLUDING ALL IMPLIED WARRANTIES OF MERCHANTABILITY AND 
FITNESS. IN NO EVENT SHALL LUCENT OR ANY OF ITS ENTITIES BE LIABLE FOR 
ANY SPECIAL, INDIRECT OR CONSEQUENTIAL DAMAGES OR ANY DAMAGES 
WHATSOEVER RESULTING FROM LOSS OF USE, DATA OR PROFITS, WHETHER IN AN 
ACTION OF CONTRACT, NEGLIGENCE OR OTHER TORTIOUS ACTION, ARISING OUT OF 
OR IN CONNECTION WITH THE USE OR PERFORMANCE OF THIS SOFTWARE. smisc.c 
License: The author of this software is David M. Gay. Copyright (C) 
1998, 1999 by Lucent Technologies All Rights Reserved Permission to 
use, copy, modify, and distribute this software and its documentation 
for any purpose and without fee is hereby granted, provided that the 
above copyright notice appear in all copies and that both that the 
copyright notice and this permission notice and warranty disclaimer 
appear in supporting documentation, and that the name of Lucent or any 
of its entities not be used in advertising or publicity pertaining to 
distribution of the software without specific, written prior 
permission. LUCENT DISCLAIMS ALL WARRANTIES WITH REGARD TO THIS 
SOFTWARE, INCLUDING ALL IMPLIED WARRANTIES OF MERCHANTABILITY AND 
FITNESS. IN NO EVENT SHALL LUCENT OR ANY OF ITS ENTITIES BE LIABLE FOR 
ANY SPECIAL, INDIRECT OR CONSEQUENTIAL DAMAGES OR ANY DAMAGES 
WHATSOEVER RESULTING FROM LOSS OF USE, DATA OR PROFITS, WHETHER IN AN 
ACTION OF CONTRACT, NEGLIGENCE OR OTHER TORTIOUS ACTION, ARISING OUT OF 
OR IN CONNECTION WITH THE USE OR PERFORMANCE OF THIS SOFTWARE. 
strtodg.c License: The author of this software is David M. Gay. 
Copyright (C) 1998-2001 by Lucent Technologies All Rights Reserved 
Permission to use, copy, modify, and distribute this software and its 
documentation for any purpose and without fee is hereby granted, 
provided that the above copyright notice appear in all copies and that 
both that the copyright notice and this permission notice and warranty 
disclaimer appear in supporting documentation, and that the name of 
Lucent or any of its entities not be used in advertising or publicity 
pertaining to distribution of the software without specific, written 
prior permission. 
LUCENT DISCLAIMS ALL WARRANTIES WITH REGARD TO THIS SOFTWARE, INCLUDING 
ALL IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS. IN NO EVENT 
SHALL LUCENT OR ANY OF ITS ENTITIES BE LIABLE FOR ANY SPECIAL, INDIRECT 
OR CONSEQUENTIAL DAMAGES OR ANY DAMAGES WHATSOEVER RESULTING FROM LOSS 
OF USE, DATA OR PROFITS, WHETHER IN AN ACTION OF CONTRACT, NEGLIGENCE 
OR OTHER TORTIOUS ACTION, ARISING OUT OF OR IN CONNECTION WITH THE USE 
OR PERFORMANCE OF THIS SOFTWARE. strtord.c License: The author of this 
software is David M. Gay. Copyright (C) 1998, 2000 by Lucent 
Technologies All Rights Reserved Permission to use, copy, modify, and 
distribute this software and its documentation for any purpose and 
without fee is hereby granted, provided that the above copyright notice 
appear in all copies and that both that the copyright notice and this 
permission notice and warranty disclaimer appear in supporting 
documentation, and that the name of Lucent or any of its entities not 
be used in advertising or publicity pertaining to distribution of the 
software without specific, written prior permission. LUCENT DISCLAIMS 
ALL WARRANTIES WITH REGARD TO THIS SOFTWARE, INCLUDING ALL IMPLIED 
WARRANTIES OF MERCHANTABILITY AND FITNESS. IN NO EVENT SHALL LUCENT OR 
ANY OF ITS ENTITIES BE LIABLE FOR ANY SPECIAL, INDIRECT OR 
CONSEQUENTIAL DAMAGES OR ANY DAMAGES WHATSOEVER RESULTING FROM LOSS OF 
USE, DATA OR PROFITS, WHETHER IN AN ACTION OF CONTRACT, NEGLIGENCE OR 
OTHER TORTIOUS ACTION, ARISING OUT OF OR IN CONNECTION WITH THE USE OR 
PERFORMANCE OF THIS SOFTWARE. sum.c License: The author of this 
software is David M. Gay. Copyright (C) 1998 by Lucent Technologies All 
Rights Reserved Permission to use, copy, modify, and distribute this 
software and its documentation for any purpose and without fee is 
hereby granted, provided that the above copyright notice appear in all 
copies and that both that the copyright notice and this permission 
notice and warranty disclaimer appear in supporting documentation, and 
that the name of Lucent or any of its entities not be used in 
advertising or publicity pertaining to distribution of the software 
without specific, written prior permission. LUCENT DISCLAIMS ALL 
WARRANTIES WITH REGARD TO THIS SOFTWARE, INCLUDING ALL IMPLIED 
WARRANTIES OF MERCHANTABILITY AND FITNESS. IN NO EVENT SHALL LUCENT OR 
ANY OF ITS ENTITIES BE LIABLE FOR ANY SPECIAL, INDIRECT OR 
CONSEQUENTIAL DAMAGES OR ANY DAMAGES WHATSOEVER RESULTING FROM LOSS OF 
USE, DATA OR PROFITS, WHETHER IN AN ACTION OF CONTRACT, NEGLIGENCE OR 
OTHER TORTIOUS ACTION, ARISING OUT OF OR IN CONNECTION WITH THE USE OR 
PERFORMANCE OF THIS SOFTWARE. ulp.c License: The author of this 
software is David M. Gay. Copyright (C) 1998, 1999 by Lucent 
Technologies All Rights Reserved Permission to use, copy, modify, and 
distribute this software and its documentation for any purpose and 
without fee is hereby granted, provided that the above copyright notice 
appear in all copies and that both that the copyright notice and this 
permission notice and warranty disclaimer appear in supporting 
documentation, and that the name of Lucent or any of its entities not 
be used in advertising or publicity pertaining to distribution of the 
software without specific, written prior permission. LUCENT DISCLAIMS 
ALL WARRANTIES WITH REGARD TO THIS SOFTWARE, INCLUDING ALL IMPLIED 
WARRANTIES OF MERCHANTABILITY AND FITNESS. IN NO EVENT SHALL LUCENT OR 
ANY OF ITS ENTITIES BE LIABLE FOR ANY SPECIAL, INDIRECT OR 
CONSEQUENTIAL DAMAGES OR ANY DAMAGES WHATSOEVER RESULTING FROM LOSS OF 
USE, DATA OR PROFITS, WHETHER IN AN ACTION OF CONTRACT, NEGLIGENCE OR 
OTHER TORTIOUS ACTION, ARISING OUT OF OR IN CONNECTION WITH THE USE OR 
PERFORMANCE OF THIS SOFTWARE. 

Progress OpenEdge v11.7 may incorporate DOM4J v1.6.1 from MetaStuff, 
Ltd.  Such technology is subject to the following terms and conditions: 
Copyright 2001-2005 (C) MetaStuff, Ltd. All Rights Reserved. 
Redistribution and use of this software and associated documentation 
("Software"), with or without modification, are permitted provided that 
the following conditions are met: 1. Redistributions of source code 
must retain copyright statements and notices. Redistributions must also 
contain a copy of this document. 2. Redistributions in binary form must 
reproduce the above copyright notice, this list of conditions and the 
following disclaimer in the documentation and/or other materials 
provided with the distribution. 3. The name "DOM4J" must not be used to 
endorse or promote products derived from this Software without prior 
written permission of MetaStuff, Ltd.  For written permission, please 
contact dom4j-info@metastuff.com. 4. Products derived from this 
Software may not be called "DOM4J" nor may "DOM4J" appear in their 
names without prior written permission of MetaStuff, Ltd. DOM4J is a 
registered trademark of MetaStuff, Ltd. 5. Due credit should be given 
to the DOM4J Project - http://www.dom4j.org THIS SOFTWARE IS PROVIDED 
BY METASTUFF, LTD. AND CONTRIBUTORS ``AS IS'' AND ANY EXPRESSED OR 
IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED 
WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE 
DISCLAIMED.  IN NO EVENT SHALL METASTUFF, LTD. OR ITS CONTRIBUTORS BE 
LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR 
CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF 
SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR 
BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, 
WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR 
OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF 
ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

Progress OpenEdge v11.7 may incorporate GraphicsMagick v1.3.14 from 
GraphicsMagick Group. Such technology is subject to the following terms 
and conditions:  
.. This text is in reStucturedText format, so it may look a bit odd. .. 
See http://docutils.sourceforge.net/rst.html for details.
======================================
GraphicsMagick Copyrights and Licenses
======================================
This file is part of the GraphicsMagick software distributed by the 
GraphicsMagick Group.
  [*Please note that the legal community considers 15 or more total 
lines of code or text (not necessarily contiguous) to
  be significant for the purposes of copyright. Repeated changes such 
as renaming a symbol has similar significance
  to changing one line of code.*]
The licenses which components of this software fall under are as 
follows.
1) In November 2002, the GraphicsMagick Group created GraphicsMagick 
from ImageMagick Studio's ImageMagick and applied the "MIT" style 
license:  Copyright (C) 2002 - 2012 GraphicsMagick Group
Permission is hereby granted, free of charge, to any person obtaining a 
copy of this software and associated documentation files (the 
"Software"), to deal in the Software without restriction, including 
without limitation the rights to use, copy, modify, merge, publish, 
distribute, sublicense, and/or sell copies of the Software, and to 
permit persons to whom the Software is furnished to do so, subject to 
the following conditions: The above copyright notice and this 
permission notice shall be included in all copies or substantial 
portions of the Software. THE SOFTWARE IS PROVIDED "AS IS", WITHOUT 
WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO 
THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND 
NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE 
LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION 
OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION 
WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
2) In October 1999, ImageMagick Studio assumed the responsibility for 
the development of ImageMagick (forking from the distribution by E. I. 
du Pont de Nemours and Company) and applied a new license:
Copyright (C) 2002 ImageMagick Studio, a non-profit organization 
dedicated to making software imaging solutions freely available. 
Permission is hereby granted, free of charge, to any person obtaining a 
copy of this software and associated documentation files 
("ImageMagick"), to deal in ImageMagick without restriction, including 
without limitation the rights to use, copy, modify, merge, publish, 
distribute, sublicense, and/or sell copies of ImageMagick, and to 
permit persons to whom the ImageMagick is furnished to do so, subject 
to the following conditions:
The above copyright notice and this permission notice shall be included 
in all copies or substantial portions of ImageMagick.
The software is provided "as is", without warranty of any kind, express 
or implied, including but not limited to the warranties of 
merchantability, fitness for a particular purpose and noninfringement.  
In no event shall ImageMagick Studio be liable for any claim, damages 
or other liability, whether in an action of contract, tort or 
otherwise, arising from, out of or in connection with ImageMagick or 
the use or other dealings in ImageMagick.
Except as contained in this notice, the name of the ImageMagick Studio 
shall not be used in advertising or otherwise to promote the sale, use 
or other dealings in ImageMagick without prior written authorization 
from the ImageMagick Studio.
3) From 1991 to October 1999 (through ImageMagick 4.2.9), ImageMagick 
was developed and distributed by E. I. du Pont de Nemours and Company: 
Copyright 1999 E. I. du Pont de Nemours and Company
Permission is hereby granted, free of charge, to any person obtaining a 
copy of this software and associated documentation files 
("ImageMagick"), to deal in ImageMagick without restriction, including 
without limitation the rights to use, copy, modify, merge, publish, 
distribute, sublicense, and/or sell copies of ImageMagick, and to 
permit persons to whom the ImageMagick is furnished to do so, subject 
to the following conditions:
The above copyright notice and this permission notice shall be included 
in all copies or substantial portions of ImageMagick.
The software is provided "as is", without warranty of any kind, express 
or implied, including but not limited to the warranties of 
merchantability, fitness for a particular purpose and noninfringement. 
In no event shall E. I. du Pont de Nemours and Company be liable for 
any claim, damages or other liability, whether in an action of 
contract, tort or otherwise, arising from, out of or in connection with 
ImageMagick or the use or other dealings in ImageMagick.
Except as contained in this notice, the name of the E. I. du Pont de 
Nemours and Company shall not be used in advertising or otherwise to 
promote the sale, use or other dealings in ImageMagick without prior 
written authorization from the E. I. du Pont de Nemours and Company.
4) The GraphicsMagick Base64Decode() and Base64Encode() functions are 
based on source code obtained from OpenSSH. This source code is 
distributed under the following license:  Copyright (c) 2000 Markus 
Friedl.  All rights reserved. 
Redistribution and use in source and binary forms, with or without 
modification, are permitted provided that the following conditions are 
met:
1. Redistributions of source code must retain the above copyright 
notice, this list of conditions and the following disclaimer.
2. Redistributions in binary form must reproduce the above copyright 
notice, this list of conditions and the following disclaimer in the 
documentation and/or other materials provided with the distribution.
THIS SOFTWARE IS PROVIDED BY THE AUTHOR \`\`AS IS\'\' AND ANY EXPRESS 
OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED 
WARRANTIES OF  MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE 
DISCLAIMED. IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR ANY DIRECT, 
INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES 
(INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR 
SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) 
HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, 
STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING 
IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE 
POSSIBILITY OF SUCH DAMAGE.
5) Many of the pattern images in coders/logo.c are derived from XFig, 
which is distributed under the following license:
  | FIG : Facility for Interactive Generation of figures 
  | Copyright (c) 1985-1988 by Supoj Sutanthavibul
  | Parts Copyright (c) 1989-2000 by Brian V. Smith
  | Parts Copyright (c) 1991 by Paul King
Any party obtaining a copy of these files is granted, free of charge, a 
full and unrestricted irrevocable, world-wide, paid up, royalty-free, 
nonexclusive right and license to deal in this software and 
documentation files (the "Software"), including without limitation the 
rights to use, copy, modify, merge, publish, distribute, sublicense, 
and/or sell copies of the Software, and to permit persons who receive 
copies from any such party to do so, with the only requirement being 
that this copyright notice remain intact.
6) The documentation for the composition operators is copied from the 
rlecomp manual page, which is authored by Rod Bogart and John W. 
Peterson. Rlecomp is part of the Utah Raster Toolkit distributed by the 
University of Michigan and the University of Utah. The copyright for 
this manual page is as follows: Copyright (c) 1986, University of Utah 
This software is copyrighted as noted below.  It may be freely copied, 
modified, and redistributed, provided that the copyright notice is 
preserved on all copies.
There is no warranty or other guarantee of fitness for this software, 
it is provided solely "as is".  Bug reports or fixes may be sent to the 
author, who may or may not act on them as he desires. 
You may not include this software in a program or other software 
product without supplying the source, or without informing the end-user 
that the source is available for no extra charge.
If you modify this software, you should include a notice giving the 
name of the person performing the modification, the date of 
modification, and the reason for such modification.
7) The source code comprising swab.c is originally derived from libtiff 
which has the following license:
  | Copyright (c) 1988-1997 Sam Leffler
  | Copyright (c) 1991-1997 Silicon Graphics, Inc.
Permission to use, copy, modify, distribute, and sell this software and 
its documentation for any purpose is hereby granted without fee, 
provided  that (i) the above copyright notices and this permission 
notice appear in all copies of the software and related documentation, 
and (ii) the names of Sam Leffler and Silicon Graphics may not be used 
in any advertising or publicity relating to the software without the 
specific, prior written permission of Sam Leffler and Silicon Graphics.
THE SOFTWARE IS PROVIDED "AS-IS" AND WITHOUT WARRANTY OF ANY KIND, 
EXPRESS, IMPLIED OR OTHERWISE, INCLUDING WITHOUT LIMITATION, ANY 
WARRANTY OF MERCHANTABILITY OR FITNESS FOR A PARTICULAR PURPOSE. IN NO 
EVENT SHALL SAM LEFFLER OR SILICON GRAPHICS BE LIABLE FOR ANY SPECIAL, 
INCIDENTAL, INDIRECT OR CONSEQUENTIAL DAMAGES OF ANY KIND, OR ANY 
DAMAGES WHATSOEVER RESULTING FROM LOSS OF USE, DATA OR PROFITS, WHETHER 
OR NOT ADVISED OF THE POSSIBILITY OF DAMAGE, AND ON ANY THEORY OF 
LIABILITY, ARISING OUT OF OR IN CONNECTION WITH THE USE OR PERFORMANCE 
OF THIS SOFTWARE.
8) The C++ API known as "Magick++", and which resides in the Magick++ 
directory, is distributed under the following license: Copyright 1999 - 
2003 Bob Friesenhahn <bfriesen@simple.dallas.tx.us>
Permission is hereby granted, free of charge, to any person obtaining a 
copy of the source files and associated documentation files 
("Magick++"), to deal in Magick++ without restriction, including 
without limitation of the rights to use, copy, modify, merge, publish, 
distribute, sublicense, and/or sell copies of Magick++, and to permit 
persons to whom the Magick++ is furnished to do so, subject to the 
following conditions:
This copyright notice shall be included in all copies or substantial 
portions of Magick++. The copyright to Magick++ is retained by its 
author and shall not be subsumed or replaced by any other copyright.
The software is provided "as is", without warranty of any kind, express 
or implied, including but not limited to the warranties of 
merchantability, fitness for a particular purpose and noninfringement. 
In no event shall Bob Friesenhahn be liable for any claim, damages or 
other liability, whether in an action of contract, tort or otherwise, 
arising from, out of or in connection with Magick++ or the use or other 
dealings in Magick++.
9) The GraphicsMagick HaldClutImagePixels() function in magick/hclut.c 
is based on source code from the HaldCLUT package by Eskil Steenberg 
(http://www.quelsolaar.com/technology/clut.html) which is distributed 
under the following license: Copyright (c) 2005 Eskil Steenberg.  All 
rights reserved.
Redistribution and use in source and binary forms, with or without 
modification, are permitted provided that the following conditions are 
met:
1. Redistributions of source code must retain the above copyright 
notice, this list of conditions and the following disclaimer.
2. Redistributions in binary form must reproduce the above copyright 
notice, this list of conditions and the following disclaimer in the 
documentation and/or other materials provided with the distribution.
THIS SOFTWARE IS PROVIDED BY THE AUTHOR \`\`AS IS\'\' AND ANY EXPRESS 
OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED 
WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE 
DISCLAIMED. IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR ANY DIRECT, 
INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES 
(INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR 
SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) 
HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, 
STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING 
IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE 
POSSIBILITY OF SUCH DAMAGE.
10)  GraphicsMagick makes use of third-party "delegate" libraries to 
support certain optional features. These libraries bear their own 
copyrights and licenses, which may be more or less restrictive than the 
GraphicsMagick license. For convenience, when GraphicsMagick is bundled 
with (or compiled with) "delegate" libraries, a copy of the licenses 
for these libraries is provided in a "licenses" directory.
-----------------------------------------------------------------------
---
... |copy|   unicode:: U+000A9 .. COPYRIGHT SIGN
Copyright |copy| GraphicsMagick Group 2002 - 2011

OVERVIEW and LEGAL ISSUES from jpeg.txt file (from GraphicsMagick): 
The Independent JPEG Group's JPEG software
==========================================
README for release 6b of 27-Mar-1998
====================================
This distribution contains the sixth public release of the Independent 
JPEG Group's free JPEG software.  You are welcome to redistribute this 
software and to use it for any purpose, subject to the conditions under 
LEGAL ISSUES, below.
Serious users of this software (particularly those incorporating it 
into larger programs) should contact IJG at jpeg-info@uunet.uu.net to 
be added to our electronic mailing list.  Mailing list members are 
notified of updates and have a chance to participate in technical 
discussions, etc.
This software is the work of Tom Lane, Philip Gladstone, Jim Boucher, 
Lee Crocker, Julian Minguillon, Luis Ortiz, George Phillips, Davide 
Rossi, Guido Vollbeding, Ge' Weijers, and other members of the 
Independent JPEG
Group. IJG is not affiliated with the official ISO JPEG standards 
committee.
OVERVIEW
========
This package contains C software to implement JPEG image compression 
and
decompression.  JPEG (pronounced "jay-peg") is a standardized 
compression
method for full-color and gray-scale images.  JPEG is intended for 
compressing "real-world" scenes; line drawings, cartoons and other non-
realistic images are not its strong suit.  JPEG is lossy, meaning that 
the output image is not exactly identical to the input image.  Hence 
you must not use JPEG if you have to have identical output bits.  
However, on typical photographic images, very good compression levels 
can be obtained with no visible change, and remarkably high compression 
levels are possible if you can tolerate a low-quality image.  For more 
details, see the references, or just experiment with various 
compression settings. This software implements JPEG baseline, extended-
sequential, and progressive compression processes.  Provision is made 
for supporting all variants of these processes, although some uncommon 
parameter settings aren't implemented yet.
For legal reasons, we are not distributing code for the arithmetic-
coding variants of JPEG; see LEGAL ISSUES.  We have made no provision 
for supporting the hierarchical or lossless processes defined in the 
standard.
We provide a set of library routines for reading and writing JPEG image 
files, plus two sample applications "cjpeg" and "djpeg", which use the 
library to perform conversion between JPEG and some other popular image 
file formats. The library is intended to be reused in other 
applications.
In order to support file conversion and viewing software, we have 
included considerable functionality beyond the bare JPEG 
coding/decoding capability; for example, the color quantization modules 
are not strictly part of JPEG decoding, but they are essential for 
output to colormapped file formats or colormapped displays.  These 
extra functions can be compiled out of the library if not required for 
a particular application.  We have also included "jpegtran", a utility 
for lossless transcoding between different JPEG processes, and 
"rdjpgcom" and "wrjpgcom", two simple applications for inserting and 
extracting textual comments in JFIF files.
The emphasis in designing this software has been on achieving 
portability and flexibility, while also making it fast enough to be 
useful.  In particular, the software is not intended to be read as a 
tutorial on JPEG.  (See the REFERENCES section for introductory 
material.)  Rather, it is intended to be reliable, portable, 
industrial-strength code.  We do not claim to have achieved that goal 
in every aspect of the software, but we strive for it.
We welcome the use of this software as a component of commercial 
products.
No royalty is required, but we do ask for an acknowledgement in product 
documentation, as described under LEGAL ISSUES.
LEGAL ISSUES
============
In plain English:
1. We don't promise that this software works.  (But if you find any 
bugs, please let us know!)
2. You can use this software for whatever you want.  You don't have to 
pay us.
3. You may not pretend that you wrote this software.  If you use it in 
a program, you must acknowledge somewhere in your documentation that 
you've used the IJG code.
In legalese:
The authors make NO WARRANTY or representation, either express or 
implied, with respect to this software, its quality, accuracy, 
merchantability, or fitness for a particular purpose.  This software is 
provided "AS IS", and you, its user, assume the entire risk as to its 
quality and accuracy.
This software is copyright (C) 1991-1998, Thomas G. Lane.
All Rights Reserved except as specified below.
Permission is hereby granted to use, copy, modify, and distribute this 
software (or portions thereof) for any purpose, without fee, subject to 
these conditions:
(1) If any part of the source code for this software is distributed, 
then this README file must be included, with this copyright and no-
warranty notice unaltered; and any additions, deletions, or changes to 
the original files must be clearly indicated in accompanying 
documentation.
(2) If only executable code is distributed, then the accompanying 
documentation must state that "this software is based in part on the 
work of the Independent JPEG Group".
(3) Permission for use of this software is granted only if the user 
accepts full responsibility for any undesirable consequences; the 
authors accept NO LIABILITY for damages of any kind.
These conditions apply to any software derived from or based on the IJG 
code, not just to the unmodified library.  If you use our work, you 
ought to acknowledge us.
Permission is NOT granted for the use of any IJG author's name or 
company name in advertising or publicity relating to this software or 
products derived from it.  This software may be referred to only as 
"the Independent JPEG Group's software".
We specifically permit and encourage the use of this software as the 
basis of commercial products, provided that all warranty or liability 
claims are assumed by the product vendor.
ansi2knr.c is included in this distribution by permission of L. Peter 
Deutsch, sole proprietor of its copyright holder, Aladdin Enterprises 
of Menlo Park, CA.
ansi2knr.c is NOT covered by the above copyright and conditions, but 
instead by the usual distribution terms of the Free Software 
Foundation; principally, that you must include source code if you 
redistribute it.  (See the file ansi2knr.c for full details.)  However, 
since ansi2knr.c is not needed as part of any program generated from 
the IJG code, this does not limit you more than the foregoing 
paragraphs do.
The Unix configuration script "configure" was produced with GNU 
Autoconf.
It is copyright by the Free Software Foundation but is freely 
distributable.
The same holds for its supporting scripts (config.guess, config.sub, 
ltconfig, ltmain.sh).  Another support script, install-sh, is copyright 
by M.I.T. but is also freely distributable.
It appears that the arithmetic coding option of the JPEG spec is 
covered by patents owned by IBM, AT&T, and Mitsubishi.  Hence 
arithmetic coding cannot legally be used without obtaining one or more 
licenses.  For this reason, support for arithmetic coding has been 
removed from the free JPEG software. (Since arithmetic coding provides 
only a marginal gain over the unpatented Huffman mode, it is unlikely 
that very many implementations will support it.)
So far as we are aware, there are no patent restrictions on the 
remaining code.
The IJG distribution formerly included code to read and write GIF 
files.
To avoid entanglement with the Unisys LZW patent, GIF reading support 
has been removed altogether, and the GIF writer has been simplified to 
produce "uncompressed GIFs".  This technique does not use the LZW 
algorithm; the resulting GIF files are larger than usual, but are 
readable by all standard GIF decoders.
We are required to state that "The Graphics Interchange Format(c) is 
the Copyright property of CompuServe Incorporated.  GIF(sm) is a 
Service Mark property of CompuServe Incorporated."

Contents of png.txt file (from GraphicsMagick):
This copy of the libpng notices is provided for your convenience.  In 
case of any discrepancy between this copy and the notices in the file 
png.h that is included in the libpng distribution, the latter shall 
prevail.
COPYRIGHT NOTICE, DISCLAIMER, and LICENSE:
If you modify libpng you may insert additional notices immediately 
following this sentence.
libpng versions 1.0.7, July 1, 2000, through 1.2.0, September 1, 2001, 
are Copyright (c) 2000 Glenn Randers-Pehrson and are distributed 
according to the same disclaimer and license as libpng-1.0.6 with the 
following individuals added to the list of Contributing Authors
   Simon-Pierre Cadieux
   Eric S. Raymond
   Gilles Vollant
and with the following additions to the disclaimer:
There is no warranty against interference with your enjoyment of the 
library or against infringement.  There is no warranty that our efforts 
or the library will fulfill any of your particular purposes or needs.  
This library is provided with all faults, and the entire risk of 
satisfactory quality, performance, accuracy, and effort is with the 
user.
libpng versions 0.97, January 1998, through 1.0.6, March 20, 2000, are 
Copyright (c) 1998, 1999 Glenn Randers-Pehrson, and are distributed 
according to the same disclaimer and license as libpng-0.96, with the 
following individuals added to the list of Contributing Authors:
   Tom Lane
   Glenn Randers-Pehrson
   Willem van Schaik
libpng versions 0.89, June 1996, through 0.96, May 1997, are Copyright 
(c) 1996, 1997 Andreas Dilger
Distributed according to the same disclaimer and license as libpng-
0.88, with the following individuals added to the list of Contributing 
Authors:
   John Bowler
   Kevin Bracey
   Sam Bushell
   Magnus Holmgren
   Greg Roelofs
   Tom Tanner
libpng versions 0.5, May 1995, through 0.88, January 1996, are 
Copyright (c) 1995, 1996 Guy Eric Schalnat, Group 42, Inc. For the 
purposes of this copyright and license, "Contributing Authors" is 
defined as the following set of individuals:
   Andreas Dilger
   Dave Martindale
   Guy Eric Schalnat
   Paul Schmidt
   Tim Wegner
The PNG Reference Library is supplied "AS IS".  The Contributing 
Authors and Group 42, Inc. disclaim all warranties, expressed or 
implied, including, without limitation, the warranties of 
merchantability and of fitness for any purpose.  The Contributing 
Authors and Group 42, Inc. assume no liability for direct, indirect, 
incidental, special, exemplary, or consequential damages, which may 
result from the use of the PNG Reference Library, even if advised of 
the possibility of such damage. Permission is hereby granted to use, 
copy, modify, and distribute this source code, or portions hereof, for 
any purpose, without fee, subject to the following restrictions:
1. The origin of this source code must not be misrepresented.
2. Altered versions must be plainly marked as such and must not be 
misrepresented as being the original source.
3. This Copyright notice may not be removed or altered from any source 
or altered source distribution.
The Contributing Authors and Group 42, Inc. specifically permit, 
without fee, and encourage the use of this source code as a component 
to supporting the PNG file format in commercial products.  If you use 
this source code in a product, acknowledgment is not required but would 
be appreciated.
A "png_get_copyright" function is available, for convenient use in 
"about" boxes and the like:
   printf("%s",png_get_copyright(NULL));
Also, the PNG logo (in PNG format, of course) is supplied in the files 
"pngbar.png" and "pngbar.jpg (88x31) and "pngnow.png" (98x31).
Libpng is OSI Certified Open Source Software.  OSI Certified Open 
Source is a certification mark of the Open Source Initiative.
Glenn Randers-Pehrson
randeg@alum.rpi.edu
September 1, 2001

Contents of tiff.txt file (from GraphicsMagick):
Copyright (c) 1988-1997 Sam Leffler
Copyright (c) 1991-1997 Silicon Graphics, Inc.
Permission to use, copy, modify, distribute, and sell this software and 
its documentation for any purpose is hereby granted without fee, 
provided that (i) the above copyright notices and this permission 
notice appear in all copies of the software and related documentation, 
and (ii) the names of Sam Leffler and Silicon Graphics may not be used 
in any advertising or publicity relating to the software without the 
specific, prior written permission of Sam Leffler and Silicon Graphics. 
THE SOFTWARE IS PROVIDED "AS-IS" AND WITHOUT WARRANTY OF ANY KIND, 
EXPRESS, IMPLIED OR OTHERWISE, INCLUDING WITHOUT LIMITATION, ANY 
WARRANTY OF MERCHANTABILITY OR FITNESS FOR A PARTICULAR PURPOSE.  IN NO 
EVENT SHALL SAM LEFFLER OR SILICON GRAPHICS BE LIABLE FOR ANY SPECIAL, 
INCIDENTAL, INDIRECT OR CONSEQUENTIAL DAMAGES OF ANY KIND, OR ANY 
DAMAGES WHATSOEVER RESULTING FROM LOSS OF USE, DATA OR PROFITS, WHETHER 
OR NOT ADVISED OF THE POSSIBILITY OF DAMAGE, AND ON ANY THEORY OF 
LIABILITY, ARISING OUT OF OR IN CONNECTION WITH THE USE OR PERFORMANCE 
OF THIS SOFTWARE. 

Contents of zlib.txt file (from GraphicsMagick):
zlib 1.1.3 is a general purpose data compression library.  All the code 
is thread safe.  The data format used by the zlib library is described 
by RFCs (Request for Comments) 1950 to 1952 in the files 
ftp://ds.internic.net/rfc/rfc1950.txt (zlib format), rfc1951.txt 
(deflate format) and rfc1952.txt (gzip format). These documents are 
also available in other formats from 
ftp://ftp.uu.net/graphics/png/documents/zlib/zdoc-index.html
All functions of the compression library are documented in the file 
zlib.h (volunteer to write man pages welcome, contact jloup@gzip.org). 
A usage example of the library is given in the file example.c which 
also tests that the library is working correctly. Another example is 
given in the file minigzip.c. The compression library itself is 
composed of all source files except example.c and minigzip.c.
To compile all files and run the test program, follow the instructions 
given at the top of Makefile. In short "make test; make install" should 
work for most machines. For Unix: "configure; make test; make install"
For MSDOS, use one of the special makefiles such as Makefile.msc.
For VMS, use Make_vms.com or descrip.mms.
Questions about zlib should be sent to <zlib@quest.jpl.nasa.gov>, or to 
Gilles Vollant <info@winimage.com> for the Windows DLL version.
The zlib home page is http://www.cdrom.com/pub/infozip/zlib/
The official zlib ftp site is ftp://ftp.cdrom.com/pub/infozip/zlib/
Before reporting a problem, please check those sites to verify that you 
have the latest version of zlib; otherwise get the latest version and 
check whether the problem still exists or not.
Mark Nelson <markn@tiny.com> wrote an article about zlib for the Jan. 
1997 issue of  Dr. Dobb's Journal; a copy of the article is available 
in http://web2.airmail.net/markn/articles/zlibtool/zlibtool.htm
The changes made in version 1.1.3 are documented in the file ChangeLog.
The main changes since 1.1.2 are:
- fix "an inflate input buffer bug that shows up on rare but persistent
  occasions" (Mark)
- fix gzread and gztell for concatenated .gz files (Didier Le Botlan)
- fix gzseek(..., SEEK_SET) in write mode
- fix crc check after a gzeek (Frank Faubert)
- fix miniunzip when the last entry in a zip file is itself a zip file
  (J Lillge)
- add contrib/asm586 and contrib/asm686 (Brian Raiter)
  See http://www.muppetlabs.com/~breadbox/software/assembly.html
- add support for Delphi 3 in contrib/delphi (Bob Dellaca)
- add support for C++Builder 3 and Delphi 3 in contrib/delphi2 (Davide 
Moretti)
- do not exit prematurely in untgz if 0 at start of block (Magnus 
Holmgren)
- use macro EXTERN instead of extern to support DLL for BeOS (Sander 
Stoks)
- added a FAQ file
plus many changes for portability.
Unsupported third party contributions are provided in directory 
"contrib". A Java implementation of zlib is available in the Java 
Development Kit 1.1 
http://www.javasoft.com/products/JDK/1.1/docs/api/Package-
java.util.zip.html
See the zlib home page http://www.cdrom.com/pub/infozip/zlib/ for 
details.
A Perl interface to zlib written by Paul Marquess 
<pmarquess@bfsec.bt.co.uk>  is in the CPAN (Comprehensive Perl Archive 
Network) sites, such as: 
ftp://ftp.cis.ufl.edu/pub/perl/CPAN/modules/by-
module/Compress/Compress-Zlib*
A Python interface to zlib written by A.M. Kuchling <amk@magnet.com> is 
available in Python 1.5 and later versions, see 
http://www.python.org/doc/lib/module-zlib.html
A zlib binding for TCL written by Andreas Kupries 
<a.kupries@westend.com> is availlable at 
http://www.westend.com/~kupries/doc/trf/man/man.html
An experimental package to read and write files in .zip format, written 
on top of zlib by Gilles Vollant <info@winimage.com>, is available at 
http://www.winimage.com/zLibDll/unzip.html and also in the 
contrib/minizip directory of zlib.
Notes for some targets:
- To build a Windows DLL version, include in a DLL project zlib.def, 
zlib.rc and all .c files except example.c and minigzip.c; compile with 
-DZLIB_DLL
  The zlib DLL support was initially done by Alessandro Iacopetti and 
is now maintained by Gilles Vollant <info@winimage.com>. Check the zlib 
DLL home page at http://www.winimage.com/zLibDll
  From Visual Basic, you can call the DLL functions which do not take a 
structure as argument: compress, uncompress and all gz* functions.
  See contrib/visual-basic.txt for more information, or get 
http://www.tcfb.com/dowseware/cmp-z-it.zip
- For 64-bit Irix, deflate.c must be compiled without any optimization. 
With -O, one libpng test fails. The test works in 32 bit mode (with the 
-n32 compiler flag). The compiler bug has been reported to SGI.
- zlib doesn't work with gcc 2.6.3 on a DEC 3000/300LX under OSF/1 2.1 
it works when compiled with cc.
- on Digital Unix 4.0D (formely OSF/1) on AlphaServer, the cc option -
std1 is necessary to get gzprintf working correctly. This is done by 
configure.
- zlib doesn't work on HP-UX 9.05 with some versions of /bin/cc. It 
works with other compilers. Use "make test" to check your compiler.
- gzdopen is not supported on RISCOS, BEOS and by some Mac compilers.
- For Turbo C the small model is supported only with reduced 
performance to avoid any far allocation; it was tested with -
DMAX_WBITS=11 -DMAX_MEM_LEVEL=3
- For PalmOs, see http://www.cs.uit.no/~perm/PASTA/pilot/software.html 
Per Harald Myrvang <perm@stud.cs.uit.no>
Acknowledgments:
  The deflate format used by zlib was defined by Phil Katz. The deflate 
and zlib specifications were written by L. Peter Deutsch. Thanks to all 
the people who reported problems and suggested various improvements in 
zlib; they are too numerous to cite here.
Copyright notice:
 (C) 1995-1998 Jean-loup Gailly and Mark Adler
This software is provided 'as-is', without any express or implied 
warranty.  In no event will the authors be held liable for any damages 
arising from the use of this software.
Permission is granted to anyone to use this software for any purpose, 
including commercial applications, and to alter it and redistribute it 
freely, subject to the following restrictions:
1. The origin of this software must not be misrepresented; you must not 
claim that you wrote the original software. If you use this software in 
a product, an acknowledgment in the product documentation would be 
appreciated but is not required.
2. Altered source versions must be plainly marked as such, and must not 
be misrepresented as being the original software.
3. This notice may not be removed or altered from any source 
distribution.
Jean-loup Gailly        Mark Adler
jloup@gzip.org          madler@alumni.caltech.edu
If you use the zlib library in a product, we would appreciate *not* 
receiving lengthy legal documents to sign. The sources are provided for 
free but without warranty of any kind.  The library has been entirely 
written by Jean-loup Gailly and Mark Adler; it does not include third-
party code.
If you redistribute modified sources, we would appreciate that you 
include in the file ChangeLog history information documenting your 
changes.

Progress OpenEdge v11.7 may incorporate International Classes for 
Unicode (International Components for Unicode) v2.4 from IBM. Such 
technology is subject to the following terms and conditions:  ICU 
License - The ICU project is licensed under the X License (see also the 
x.org original), which is compatible with GPL but non-copyleft. The 
license allows ICU to be incorporated into a wide variety of software 
projects using the GPL license. The X license is compatible with the 
GPL, while also allowing ICU to be incorporated into non-open source 
products.  License ICU License - ICU 1.8.1 and later COPYRIGHT AND 
PERMISSION NOTICE Copyright (c) 1995-2003 International Business 
Machines Corporation and others All rights reserved. Permission is 
hereby granted, free of charge, to any person obtaining a copy of this 
software and associated documentation files (the "Software"), to deal 
in the Software without restriction, including without limitation the 
rights to use, copy, modify, merge, publish, distribute, and/or sell 
copies of the Software, and to permit persons to whom the Software is 
furnished to do so, provided that the above copyright notice(s) and 
this permission notice appear in all copies of the Software and that 
both the above copyright notice(s) and this permission notice appear in 
supporting documentation. THE SOFTWARE IS PROVIDED "AS IS", WITHOUT 
WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO 
THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND 
NONINFRINGEMENT OF THIRD PARTY RIGHTS. IN NO EVENT SHALL THE COPYRIGHT 
HOLDER OR HOLDERS INCLUDED IN THIS NOTICE BE LIABLE FOR ANY CLAIM, OR 
ANY SPECIAL INDIRECT OR CONSEQUENTIAL DAMAGES, OR ANY DAMAGES 
WHATSOEVER RESULTING FROM LOSS OF USE, DATA OR PROFITS, WHETHER IN AN 
ACTION OF CONTRACT, NEGLIGENCE OR OTHER TORTIOUS ACTION, ARISING OUT OF 
OR IN CONNECTION WITH THE USE OR PERFORMANCE OF THIS SOFTWARE. Except 
as contained in this notice, the name of a copyright holder shall not 
be used in advertising or otherwise to promote the sale, use or other 
dealings in this Software without prior written authorization of the 
copyright holder.
-----------------------------------------------------------------------
--
All trademarks and registered trademarks mentioned herein are the 
property of their respective owners.

Progress OpenEdge v11.7 may incorporate International Components for 
Unicode v4.8.0. Such technology is subject to the following terms and 
conditions: ICU License - ICU 1.8.1 and later COPYRIGHT AND PERMISSION 
NOTICE Copyright (c) 1995-2011 International Business Machines 
Corporation and others All rights reserved. 
Permission is hereby granted, free of charge, to any person obtaining a 
copy of this software and associated documentation files (the 
"Software"), to deal in the Software without restriction, including 
without limitation the rights to use, copy, modify, merge, publish, 
distribute, and/or sell copies of the Software, and to permit persons 
to whom the Software is furnished to do so, provided that the above 
copyright notice(s) and this permission notice appear in all copies of 
the Software and that both the above copyright notice(s) and this 
permission notice appear in supporting documentation. 
THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS 
OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF 
MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT 
OF THIRD PARTY RIGHTS. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR 
HOLDERS INCLUDED IN THIS NOTICE BE LIABLE FOR ANY CLAIM, OR ANY SPECIAL 
INDIRECT OR CONSEQUENTIAL DAMAGES, OR ANY DAMAGES WHATSOEVER RESULTING 
FROM LOSS OF USE, DATA OR PROFITS, WHETHER IN AN ACTION OF CONTRACT, 
NEGLIGENCE OR OTHER TORTIOUS ACTION, ARISING OUT OF OR IN CONNECTION 
WITH THE USE OR PERFORMANCE OF THIS SOFTWARE. 
Except as contained in this notice, the name of a copyright holder 
shall not be used in advertising or otherwise to promote the sale, use 
or other dealings in this Software without prior written authorization 
of the copyright holder. 
All trademarks and registered trademarks mentioned herein are the 
property of their respective owners. 

Progress OpenEdge v11.7 may incorporate java.net args4j v2.0.12 from 
Kohsuke Kawaguchi.  Such technology is subject to the following terms 
and conditions: Copyright (c) 2003, Kohsuke Kawaguchi All rights 
reserved. Redistribution and use in source and binary forms, with or 
without modification, are permitted provided that the following 
conditions are met:  *Redistributions of source code must retain the 
above copyright notice, this list of conditions and the following 
disclaimer. *Redistributions in binary form must reproduce the above 
copyright notice, this list of conditions and the following disclaimer 
in the documentation and/or other materials provided with the 
distribution. THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND 
CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, 
BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND 
FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE 
COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, 
INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, 
BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS 
OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND 
ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR 
TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE 
USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH 
DAMAGE.

Progress OpenEdge v11.7 may incorporate Jaxen v1.1-beta-3 from 
jaxen.org.  Such technology is subject to the following terms and 
conditions:  Project License $Id: LICENSE.txt 1128 2006-02-05 21:49:04Z 
elharo $ Copyright 2003-2006 The Werken Company. All Rights Reserved. 
Redistribution and use in source and binary forms, with or without 
modification, are permitted provided that the following conditions are 
met: Redistributions of source code must retain the above copyright 
notice, this list of conditions and the following disclaimer. 
Redistributions in binary form must reproduce the above copyright 
notice, this list of conditions and the following disclaimer in the 
documentation and/or other materials provided with the distribution. 
Neither the name of the Jaxen Project nor the names of its contributors 
may be used to endorse or promote products derived from this software 
without specific prior written permission. THIS SOFTWARE IS PROVIDED BY 
THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR 
IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED 
WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE 
DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE 
LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR 
CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF 
SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR 
BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, 
WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR 
OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF 
ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

Progress OpenEdge v11.7 may incorporate Jing 20030619 (as part of 1PDT 
Progress Extensions for Eclipse (PEX) v2.6.0).  Such technology is 
subject to the following terms and conditions:  Jing Copying 
Conditions. Copyright (c) 2001-2003 Thai Open Source Software Center 
Ltd. All rights reserved. Redistribution and use in source and binary 
forms, with or without modification, are permitted provided that the 
following conditions are met: Redistributions of source code must 
retain the above copyright notice, this list of conditions and the 
following disclaimer. Redistributions in binary form must reproduce the 
above copyright notice, this list of conditions and the following 
disclaimer in the documentation and/or other materials provided with 
the distribution. Neither the name of the Thai Open Source Software 
Center Ltd nor the names of its contributors may be used to endorse or 
promote products derived from this software without specific prior 
written permission. THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS 
AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, 
INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF 
MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN 
NO EVENT SHALL THE REGENTS OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, 
INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES 
(INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR 
SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) 
HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, 
STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING 
IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE 
POSSIBILITY OF SUCH DAMAGE. Third-party JARs - This distribution 
includes some additional JAR files, which have their own copying 
conditions: 
saxon.jar Comes from the Saxon 6.5.2 distribution and is covered by 
these conditions xercesImpl.jar xml-apis.jar Come from the Xerces-J 
2.4.0 distribution and are covered by the Apache Software License 
isorelax.jar Comes from ISO RELAX 2003/01/08 distribution and is 
covered by the following license: Copyright (c) 2001-2002, SourceForge 
ISO-RELAX Project (ASAMI Tomoharu, Daisuke Okajima, Kohsuke Kawaguchi, 
and MURATA Makoto) Permission is hereby granted, free of charge, to any 
person obtaining a copy of this software and associated documentation 
files (the "Software"), to deal in the Software without restriction, 
including without limitation the rights to use, copy, modify, merge, 
publish, distribute, sublicense, and/or sell copies of the Software, 
and to permit persons to whom the Software is furnished to do so, 
subject to the following conditions: The above copyright notice and 
this permission notice shall be included in all copies or substantial 
portions of the Software. THE SOFTWARE IS PROVIDED "AS IS", WITHOUT 
WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO 
THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND 
NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE 
LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION 
OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION 
WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

Progress OpenEdge v11.7 may incorporate Sun's Jmaki framework v1.0 and 
Sun's Jmaki framework v1.0.3 from Sun Microsystems, Inc.  Such 
technology is subject to the following terms and conditions: Copyright 
1994-2006 Sun Microsystems, Inc. All Rights Reserved. Redistribution 
and use in source and binary forms, with or without modification, are 
permitted provided that the following conditions are met: 
Redistribution of source code must retain the above copyright notice, 
this list of conditions and the following disclaimer. Redistribution in 
binary form must reproduce the above copyright notice, this list of 
conditions and the following disclaimer in the documentation and/or 
other materials provided with the distribution. Neither the name of Sun 
Microsystems, Inc. or the names of contributors may be used to endorse 
or promote products derived from this software without specific prior 
written permission. This software is provided "AS IS," without a 
warranty of any kind. ALL EXPRESS OR IMPLIED CONDITIONS, 
REPRESENTATIONS AND WARRANTIES, INCLUDING ANY IMPLIED WARRANTY OF 
MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE OR NON-INFRINGEMENT, 
ARE HEREBY EXCLUDED. SUN MICROSYSTEMS, INC. ("SUN") AND ITS LICENSORS 
SHALL NOT BE LIABLE FOR ANY DAMAGES SUFFERED BY LICENSEE AS A RESULT OF 
USING, MODIFYING OR DISTRIBUTING THIS SOFTWARE OR ITS DERIVATIVES. IN 
NO EVENT WILL SUN OR ITS LICENSORS BE LIABLE FOR ANY LOST REVENUE, 
PROFIT OR DATA, OR FOR DIRECT, INDIRECT, SPECIAL, CONSEQUENTIAL, 
INCIDENTAL OR PUNITIVE DAMAGES, HOWEVER CAUSED AND REGARDLESS OF THE 
THEORY OF LIABILITY, ARISING OUT OF THE USE OF OR INABILITY TO USE THIS 
SOFTWARE, EVEN IF SUN HAS BEEN ADVISED OF THE POSSIBILITY OF SUCH 
DAMAGES. You acknowledge that this software is not designed, licensed 
or intended for use in the design, construction, operation or 
maintenance of any nuclear facility.
	
Progress OpenEdge v11.7 may incorporate Jmaki framework v1.8.0 from Sun 
Microsystems, Inc.  Such technology is subject to the following terms 
and conditions: Copyright 1994-2006 Sun Microsystems, Inc. All Rights 
Reserved. Redistribution and use in source and binary forms, with or 
without modification, are permitted provided that the following 
conditions are met: * Redistribution of source code must retain the 
above copyright notice, this list of conditions and the following 
disclaimer. * Redistribution in binary form must reproduce the above 
copyright notice, this list of conditions and the following disclaimer 
in the documentation and/or other materials provided with the 
distribution. Neither the name of Sun Microsystems, Inc. or the names 
of contributors may be used to endorse or promote products derived from 
this software without specific prior written permission. This software 
is provided "AS IS," without a warranty of any kind. ALL EXPRESS OR 
IMPLIED CONDITIONS, REPRESENTATIONS AND WARRANTIES, INCLUDING ANY 
IMPLIED WARRANTY OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE 
OR NON-INFRINGEMENT, ARE HEREBY EXCLUDED. SUN MICROSYSTEMS, INC. 
("SUN") AND ITS LICENSORS SHALL NOT BE LIABLE FOR ANY DAMAGES SUFFERED 
BY LICENSEE AS A RESULT OF USING, MODIFYING OR DISTRIBUTING THIS 
SOFTWARE OR ITS DERIVATIVES. IN NO EVENT WILL SUN OR ITS LICENSORS BE 
LIABLE FOR ANY LOST REVENUE, PROFIT OR DATA, OR FOR DIRECT, INDIRECT, 
SPECIAL, CONSEQUENTIAL, INCIDENTAL OR PUNITIVE DAMAGES, HOWEVER CAUSED 
AND REGARDLESS OF THE THEORY OF LIABILITY, ARISING OUT OF THE USE OF OR 
INABILITY TO USE THIS SOFTWARE, EVEN IF SUN HAS BEEN ADVISED OF THE 
POSSIBILITY OF SUCH DAMAGES. You acknowledge that this software is not 
designed, licensed or intended for use in the design, construction, 
operation or maintenance of any nuclear facility.

Progress OpenEdge v11.7 may incorporate JSTL v1.0 from Sun 
Microsystems, Inc. Such technologies are subject to the following terms 
and conditions: Code sample License Copyright 1994-2006 Sun 
Microsystems, Inc. All Rights Reserved. Redistribution and use in 
source and binary forms, with or without modification, are permitted 
provided that the following conditions are met: Redistribution of 
source code must retain the above copyright notice, this list of 
conditions and the following disclaimer. Redistribution in binary form 
must reproduce the above copyright notice, this list of conditions and 
the following disclaimer in the documentation and/or other materials 
provided with the distribution. Neither the name of Sun Microsystems, 
Inc. or the names of contributors may be used to endorse or promote 
products derived from this software without specific prior written 
permission. This software is provided "AS IS," without a warranty of 
any kind. ALL EXPRESS OR IMPLIED CONDITIONS, REPRESENTATIONS AND 
WARRANTIES, INCLUDING ANY IMPLIED WARRANTY OF MERCHANTABILITY, FITNESS 
FOR A PARTICULAR PURPOSE OR NON-INFRINGEMENT, ARE HEREBY EXCLUDED. SUN 
MICROSYSTEMS, INC. ("SUN") AND ITS LICENSORS SHALL NOT BE LIABLE FOR 
ANY DAMAGES SUFFERED BY LICENSEE AS A RESULT OF USING, MODIFYING OR 
DISTRIBUTING THIS SOFTWARE OR ITS DERIVATIVES. IN NO EVENT WILL SUN OR 
ITS LICENSORS BE LIABLE FOR ANY LOST REVENUE, PROFIT OR DATA, OR FOR 
DIRECT, INDIRECT, SPECIAL, CONSEQUENTIAL, INCIDENTAL OR PUNITIVE 
DAMAGES, HOWEVER CAUSED AND REGARDLESS OF THE THEORY OF LIABILITY, 
ARISING OUT OF THE USE OF OR INABILITY TO USE THIS SOFTWARE, EVEN IF 
SUN HAS BEEN ADVISED OF THE POSSIBILITY OF SUCH DAMAGES. You 
acknowledge that this software is not designed, licensed or intended 
for use in the design, construction, operation or maintenance of any 
nuclear facility.  

Progress OpenEdge v11.7 may incorporate Looks v2.1.2 from JGoodies.  
Such technology is subject to the following terms and conditions: The 
BSD License for the JGoodies Looks - Copyright (c) 2001-2007 JGoodies 
Karsten Lentzsch. All rights reserved. Redistribution and use in source 
and binary forms, with or without modification, are permitted provided 
that the following conditions are met: o Redistributions of source code 
must retain the above copyright notice, this list of conditions and the 
following disclaimer. Redistributions in binary form must reproduce the 
above copyright notice, this list of conditions and the following 
disclaimer in the documentation and/or other materials provided with 
the distribution. Neither the name of JGoodies Karsten Lentzsch nor the 
names of its contributors may be used to endorse or promote products 
derived from this software without specific prior written permission. 
THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS 
IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED 
TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A 
PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT 
OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, 
SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT 
LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, 
DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY 
THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT 
(INCLUDING NEGLIGENCE OR  OTHERWISE) ARISING IN ANY WAY OUT OF THE USE 
OF THIS SOFTWARE,  EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

Progress OpenEdge v11.7 may incorporate OpenSSL v1.1.0c from The 
OpenSSL Project. Such technology is subject to the following terms and 
conditions:  
LICENSE ISSUES
==============
The OpenSSL toolkit stays under a dual license, i.e. both the 
conditions of the OpenSSL License and the original SSLeay license apply 
to the toolkit.
See below for the actual license texts.
OpenSSL License
====================================================================
Copyright (c) 1998-2016 The OpenSSL Project.  All rights reserved.
Redistribution and use in source and binary forms, with or without 
modification, are permitted provided that the following conditions are 
met:
1. Redistributions of source code must retain the above copyright 
notice, this list of conditions and the following disclaimer. 
2. Redistributions in binary form must reproduce the above copyright 
notice, this list of conditions and the following disclaimer in the 
documentation and/or other materials provided with the distribution.
3. All advertising materials mentioning features or use of this 
software must display the following acknowledgment:
"This product includes software developed by the OpenSSL Project   for 
use in the OpenSSL Toolkit. (http://www.openssl.org/)"
4. The names "OpenSSL Toolkit" and "OpenSSL Project" must not be used 
to endorse or promote products derived from this software without prior 
written permission. For written permission, please contact openssl-
core@openssl.org.
5. Products derived from this software may not be called "OpenSSL" nor 
may "OpenSSL" appear in their names without prior written permission of 
the OpenSSL Project.
6. Redistributions of any form whatsoever must retain the following 
acknowledgment:
"This product includes software developed by the OpenSSL Project for 
use in the OpenSSL Toolkit (http://www.openssl.org/)"
THIS SOFTWARE IS PROVIDED BY THE OpenSSL PROJECT ``AS IS'' AND ANY 
EXPRESSED OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE 
IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR 
PURPOSE ARE DISCLAIMED.  IN NO EVENT SHALL THE OpenSSL PROJECT OR ITS 
CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, 
EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, 
PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED 
AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, 
OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF 
THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH 
DAMAGE.
====================================================================
This product includes cryptographic software written by Eric Young 
(eay@cryptsoft.com).  This product includes software written by Tim 
Hudson (tjh@cryptsoft.com).
Original SSLeay License
-----------------------
Copyright (C) 1995-1998 Eric Young (eay@cryptsoft.com) All rights 
reserved.
This package is an SSL implementation written by Eric Young 
(eay@cryptsoft.com).
The implementation was written so as to conform with Netscapes SSL.
This library is free for commercial and non-commercial use as long as 
the following conditions are aheared to.  The following conditions 
apply to all code found in this distribution, be it the RC4, RSA, 
lhash, DES, etc., code; not just the SSL code.  The SSL documentation 
included with this distribution is covered by the same copyright terms 
except that the holder is Tim Hudson (tjh@cryptsoft.com).
Copyright remains Eric Young's, and as such any Copyright notices in 
the code are not to be removed.
If this package is used in a product, Eric Young should be given 
attribution as the author of the parts of the library used.
This can be in the form of a textual message at program startup or in 
documentation (online or textual) provided with the package.
Redistribution and use in source and binary forms, with or without 
modification, are permitted provided that the following conditions are 
met:
1. Redistributions of source code must retain the copyright notice, 
this list of conditions and the following disclaimer.
2. Redistributions in binary form must reproduce the above copyright 
notice, this list of conditions and the following disclaimer in the 
documentation and/or other materials provided with the distribution.
3. All advertising materials mentioning features or use of this 
software must display the following acknowledgement: "This product 
includes cryptographic software written by Eric Young 
(eay@cryptsoft.com)"
The word 'cryptographic' can be left out if the rouines from the 
library being used are not cryptographic related :-).
4. If you include any Windows specific code (or a derivative thereof) 
from the apps directory (application code) you must include an 
acknowledgement:
"This product includes software written by Tim Hudson 
(tjh@cryptsoft.com)"
THIS SOFTWARE IS PROVIDED BY ERIC YOUNG ``AS IS'' AND ANY EXPRESS OR 
IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED 
WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE 
DISCLAIMED.  IN NO EVENT SHALL THE AUTHOR OR CONTRIBUTORS BE LIABLE FOR 
ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL 
DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS 
OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) 
HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, 
STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING 
IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE 
POSSIBILITY OF SUCH DAMAGE.
The licence and distribution terms for any publically available version 
or derivative of this code cannot be changed.  i.e. this code cannot 
simply be copied and put under another distribution licence [including 
the GNU Public Licence.]

Progress OpenEdge v11.7 may incorporate SLF4J v1.6.2 and SLF4J Log4J 
v1.6.1 from QOS.  Such technology is subject to the following terms and 
conditions: Copyright (c) 2004-2008 QOS.ch All rights reserved. 
Permission is hereby granted, free  of charge, to any person obtaining 
a  copy  of this  software  and  associated  documentation files  (the 
"Software"), to  deal in  the Software without  restriction, including 
without limitation  the rights to  use, copy, modify,  merge, publish, 
distribute,  sublicense, and/or sell  copies of  the Software,  and to 
permit persons to whom the Software  is furnished to do so, subject to 
the following conditions: The  above  copyright  notice  and  this 
permission  notice  shall  be included in all copies or substantial 
portions of the Software. THE SOFTWARE IS PROVIDED "AS IS", WITHOUT 
WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO 
THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND 
NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE 
LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION 
OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION 
WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

Progress OpenEdge v11.7 may incorporate SLF4J v1.7.7 from QOS.  Such 
technology is subject to the following terms and conditions: Copyright 
(c) 2004-2013 QOS.ch All rights reserved. Permission is hereby granted, 
free  of charge, to any person obtaining a  copy  of this  software  
and  associated  documentation files  (the "Software"), to  deal in  
the Software without  restriction, including without limitation  the 
rights to  use, copy, modify,  merge, publish, distribute,  sublicense, 
and/or sell  copies of  the Software,  and to permit persons to whom 
the Software  is furnished to do so, subject to the following 
conditions:  The  above  copyright  notice  and  this permission  
notice  shall  be included in all copies or substantial portions of the 
Software. THE  SOFTWARE IS  PROVIDED  "AS  IS", WITHOUT  WARRANTY  OF 
ANY  KIND, EXPRESS OR  IMPLIED, INCLUDING  BUT NOT LIMITED  TO THE  
WARRANTIES OF MERCHANTABILITY,    FITNESS    FOR    A   PARTICULAR    
PURPOSE    AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR 
COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, 
WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE,  ARISING FROM, OUT 
OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN 
THE SOFTWARE.

Progress OpenEdge v11.7 may incorporate STAX API (JSR 173) v3.1.1 from 
Woodstox Project.  Such technology is subject to the following terms 
and conditions: Copyright (c) 2004-2010, Woodstox Project 
(http://woodstox.codehaus.org/)All rights reserved.
Redistribution and use in source and binary forms, with or without 
modification, are permitted provided that the following conditions are 
met:
1. Redistributions of source code must retain the above copyright 
notice, this list of conditions and the following disclaimer.
2. Redistributions in binary form must reproduce the above copyright 
notice, this list of conditions and the following disclaimer in the 
documentation and/or other materials provided with the distribution.
3. Neither the name of the Woodstox XML Processor nor the names of its 
contributors may be used to endorse or promote products derived from 
this software without specific prior written permission.
THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS 
IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED 
TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A 
PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT 
OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, 
SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT 
LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, 
DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY 
THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT 
(INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE 
OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

Progress OpenEdge v11.7 may incorporate Trang 20030619 (as part of 1PDT 
Progress Extensions for Eclipse (PEX) v2.6.0).  Such technology is 
subject to the following terms and conditions:  Copyright (c) 2002, 
2003 Thai Open Source Software Center Ltd. All rights reserved. 
Redistribution and use in source and binary forms, with or without 
modification, are permitted provided that the following conditions are 
met: Redistributions of source code must retain the above copyright 
notice, this list of conditions and the following disclaimer. 
Redistributions in binary form must reproduce the above copyright 
notice, this list of conditions and the following disclaimer in the 
documentation and/or other materials provided with the distribution. 
Neither the name of the Thai Open Source Software Center Ltd nor the 
names of its contributors may be used to endorse or promote products 
derived from this software without specific prior written permission. 
THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS 
IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED 
TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A 
PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE REGENTS OR 
CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, 
EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, 
PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR 
PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF 
LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING 
NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS 
SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

Progress OpenEdge v11.7 may incorporate Xpp3 v1.1.3.4 from Indiana 
University (as part of 1PDT Progress Extensions for Eclipse (PEX) 
v2.6.0).  Such technology is subject to the following terms and 
conditions: Indiana University Extreme! Lab Software License Version 
1.1.1 Copyright (c) 2002 Extreme! Lab, Indiana University. All rights 
reserved. Redistribution and use in source and binary forms, with or 
without modification, are permitted provided that the following 
conditions are met: 1. Redistributions of source code must retain the 
above copyright notice, this list of conditions and the following 
disclaimer. 2. Redistributions in binary form must reproduce the above 
copyright notice, this list of conditions and the following disclaimer 
in the documentation and/or other materials provided with the 
distribution. 3. The end-user documentation included with the 
redistribution, if any, must include the following acknowledgment:  
"This product includes software developed by the Indiana University 
Extreme! Lab (http://www.extreme.indiana.edu/)." Alternately, this 
acknowledgment may appear in the software itself, if and wherever such 
third-party acknowledgments normally appear. 4. The names "Indiana 
Univeristy" and "Indiana Univeristy Extreme! Lab" must not be used to 
endorse or promote products derived from this software without prior 
written permission. For written permission, please contact 
http://www.extreme.indiana.edu/. 5. Products derived from this software 
may not use "Indiana Univeristy" name nor may "Indiana Univeristy" 
appear in their name, without prior written permission of the Indiana 
University. THIS SOFTWARE IS PROVIDED "AS IS" AND ANY EXPRESSED OR 
IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED 
WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE 
DISCLAIMED. IN NO EVENT SHALL THE AUTHORS, COPYRIGHT HOLDERS OR ITS 
CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, 
EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, 
PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR 
PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF 
LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING 
NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS 
SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

Progress OpenEdge v11.7 may incorporate XStream v1.3.1 from XStream.  
Such technology is subject to the following terms and conditions:  
Copyright (c) 2003-2006, Joe Walnes Copyright (c) 2006-2007, XStream 
Committers All rights reserved. Redistribution and use in source and 
binary forms, with or without modification, are permitted provided that 
the following conditions are met: Redistributions of source code must 
retain the above copyright notice, this list of conditions and the 
following disclaimer. Redistributions in binary form must reproduce the 
above copyright notice, this list of conditions and the following 
disclaimer in the documentation and/or other materials provided with 
the distribution. Neither the name of XStream nor the names of its 
contributors may be used to endorse or promote products derived from 
this software without specific prior written permission. THIS SOFTWARE 
IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY 
EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE 
IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR 
PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR 
CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, 
EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, 
PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR 
PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF 
LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING 
NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS 
SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

Progress OpenEdge v11.7 may incorporate YAJL v0.4.0 from Lloyd Hilaiel.  
Such technology is subject to the following terms and conditions:  
Copyright 2007, Lloyd Hilaiel. Redistribution and use in source and 
binary forms, with or without modification, are permitted provided that 
the following conditions are met: 1. Redistributions of source code 
must retain the above copyright notice, this list of conditions and the 
following disclaimer. 2. Redistributions in binary form must reproduce 
the above copyright notice, this list of conditions and the following 
disclaimer in the documentation and/or other materials provided with 
the distribution. 3. Neither the name of Lloyd Hilaiel nor the names of 
its contributors may be used to endorse or promote products derived 
from this software without specific prior written permission. THIS 
SOFTWARE IS PROVIDED BY THE AUTHOR ``AS IS'' AND ANY EXPRESS OR IMPLIED 
WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF 
MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN 
NO EVENT SHALL THE AUTHOR BE LIABLE FOR ANY DIRECT, INDIRECT, 
INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, 
BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS 
OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND 
ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR 
TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE 
USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH 
DAMAGE.

Progress OpenEdge v11.7 may incorporate GNU ZLIB v1.1.4 and GNU ZLIB 
v1.2.3 from Jean-loup Gailiy & Mark Alder.  Such technology is subject 
to the following terms and conditions: Copyright notice: (C) 1995-2002 
Jean-loup Gailly and Mark Adler This software is provided 'as-is', 
without any express or implied warranty.  In no event will the authors 
be held liable for any damages arising from the use of this software. 
Permission is granted to anyone to use this software for any purpose, 
including commercial applications, and to alter it and redistribute it 
freely, subject to the following restrictions: 1. The origin of this 
software must not be misrepresented; you must not claim that you wrote 
the original software. If you use this software in a product, an 
acknowledgment in the product documentation would be appreciated but is 
not required. 2. Altered source versions must be plainly marked as 
such, and must not be misrepresented as being the original software. 3. 
This notice may not be removed or altered from any source distribution. 
  Jean-loup Gailly        Mark Adler
  jloup@gzip.org          madler@alumni.caltech.edu

Progress OpenEdge v11.7 may incorporate ZLIB.NET Free v1.0.4 from 
ComponentAce.  Such technology is subject to the following terms and 
conditions: Copyright (c) 2006-2007, ComponentAce 
http://www.componentace.com All rights reserved. Redistribution and use 
in source and binary forms, with or without modification, are permitted 
provided that the following conditions are met: Redistributions of 
source code must retain the above copyright notice, this list of 
conditions and the following disclaimer. Redistributions in binary form 
must reproduce the above copyright notice, this list of conditions and 
the following disclaimer in the documentation and/or other materials 
provided with the distribution. Neither the name of ComponentAce nor 
the names of its contributors may be used to endorse or promote 
products derived from this software without specific prior written 
permission. THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND 
CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, 
BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND 
FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE 
COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, 
INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, 
BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS 
OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND 
ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR 
TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE 
USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH 
DAMAGE.

Progress OpenEdge v11.7 may incorporate LALR Parser Generator in Java 
v0.10k. Such technology is subject to the following terms and 
conditions:  CUP Parser Generator Copyright Notice, License, and 
Disclaimer - Copyright 1996-1999 by Scott Hudson, Frank Flannery, C. 
Scott Ananian - Permission to use, copy, modify, and distribute this 
software and its documentation for any purpose and without fee is 
hereby granted, provided that the above copyright notice appear in all 
copies and that both the copyright notice and this permission notice 
and warranty disclaimer appear in supporting documentation, and that 
the names of the authors or their employers not be used in advertising 
or publicity pertaining to distribution of the software without 
specific, written prior permission.  The authors and their employers 
disclaim all warranties with regard to this software, including all 
implied warranties of merchantability and fitness. In no event shall 
the authors or their employers be liable for any special, indirect or 
consequential damages or any damages whatsoever resulting from loss of 
use, data or profits, whether in an action of contract, negligence or 
other tortious action, arising out of or in connection with the use or 
performance of this software. 

Progress OpenEdge v11.7 may incorporate Scintilla v3.6.2 from Neil 
Hodgson.  Such technology is subject to the following terms and 
conditions: License for Scintilla and SciTE
Copyright 1998-2003 by Neil Hodgson <neilh@scintilla.org>
All Rights Reserved 
Permission to use, copy, modify, and distribute this software and its 
documentation for any purpose and without fee is hereby granted, 
provided that the above copyright notice appear in all copies and that 
both that copyright notice and this permission notice appear in 
supporting documentation. 
NEIL HODGSON DISCLAIMS ALL WARRANTIES WITH REGARD TO THIS SOFTWARE, 
INCLUDING ALL IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS, IN NO 
EVENT SHALL NEIL HODGSON BE LIABLE FOR ANY SPECIAL, INDIRECT OR 
CONSEQUENTIAL DAMAGES OR ANY DAMAGES WHATSOEVER RESULTING FROM LOSS OF 
USE, DATA OR PROFITS, WHETHER IN AN ACTION OF CONTRACT, NEGLIGENCE OR 
OTHER TORTIOUS ACTION, ARISING OUT OF OR IN CONNECTION WITH THE USE OR 
PERFORMANCE OF THIS SOFTWARE. 

Progress OpenEdge v11.7 may incorporate the RSA Data Security, Inc. MD5 
Message-Digest Algorithm. Copyright (c)1991-2, RSA Data Security, Inc. 
Created 1991. All rights reserved. (MD5 Encryption Library v3.0 and MD5 
Encryption vMD5C.C) These technologies are subject to the following 
terms and conditions:  RSA Data Security MD5 message-digest algorithm 
RSA Data Security, Inc. MD5C.C - RSA Data Security, Inc., MD5 message-
digest algorithm Copyright (C) 1991-2, RSA Data Security, Inc. Created 
1991. All rights reserved. License to copy and use this software is 
granted provided that it is identified as the "RSA Data Security, Inc. 
MD5 Message-Digest Algorithm" in all material mentioning or referencing 
this software or this function. License is also granted to make and use 
derivative works provided that such works are identified as "derived 
from the RSA Data Security, Inc. MD5 Message-Digest Algorithm" in all 
material mentioning or referencing the derived work. RSA Data Security, 
Inc. makes no representations concerning either the merchantability of 
this software or the suitability of this software for any particular 
purpose. It is provided "as is" without express or implied warranty of 
any kind. These notices must be retained in any copies of any part of 
this documentation and/or software.

Progress OpenEdge v11.7 may incorporate Java Encoder v1.2 from OWASP 
Foundation.  Such technology is subject to the following terms and 
conditions:
Copyright (c) 2015 Jeff Ichnowski All rights reserved.
Redistribution and use in source and binary forms, with or without 
modification, are permitted provided that the following conditions are 
met:
    * Redistributions of source code must retain the above copyright 
notice, this list of conditions and the following disclaimer.
    * Redistributions in binary form must reproduce the above copyright 
notice, this list of conditions and the following disclaimer in the 
documentation and/or other materials provided with the distribution.
    * Neither the name of the OWASP nor the names of its contributors 
may be used to endorse or promote products derived from this software 
without specific prior written permission. 
THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS 
IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED 
TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A 
PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT 
HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, 
SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT 
LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, 
DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY 
THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT 
(INCLUDING NEGLIGENCE OR OTHERWISE)
ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF 
THE POSSIBILITY OF SUCH DAMAGE.

Progress OpenEdge v11.7 may incorporate ASM Jar File vv5.0.4 from 
INRIA, France Telecom.  Such technology is subject to the following 
terms and conditions:
Copyright (c) 2000-2011 INRIA, France Telecom All rights reserved.
Redistribution and use in source and binary forms, with or without 
modification, are permitted provided that the following conditions are 
met:
1. Redistributions of source code must retain the above copyright 
notice, this list of conditions and the following disclaimer.
2. Redistributions in binary form must reproduce the above copyright 
notice, this list of conditions and the following disclaimer in the 
documentation and/or other materials provided with the distribution.
3. Neither the name of the copyright holders nor the names of its 
contributors may be used to endorse or promote products derived from 
this software without specific prior written permission.
THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS 
IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED 
TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A 
PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT 
OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, 
SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT 
LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, 
DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY 
THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT 
(INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE 
OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

Progress OpenEdge v11.7 may incorporate slf4j API v1.7.13 (as part of 
Progress 1PAdapters v1.6). Such technology is subject to the following 
terms and conditions: Copyright (c) 2004-2014 QOS.ch All rights 
reserved.
Permission is hereby granted, free of charge, to any person obtaining a 
copy of this software and associated documentation files (the 
"Software"), to deal in the Software without restriction, including 
without limitation the rights to use, copy, modify, merge, publish, 
distribute, sublicense, and/or sell copies of the Software, and to 
permit persons to whom the Software is furnished to do so, subject to 
the following conditions:
The above copyright notice and this permission notice shall be included 
in all copies or substantial portions of the Software.
THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS 
OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF 
MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. 
IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY 
CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, 
TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE 
SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

Updated 3/16/2017

