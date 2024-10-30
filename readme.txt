=== JoeBooking - Time Slot Booking ===
Contributors: HitCode
Tags: appointment booking, appointment scheduling, time slot booking, client scheduling, customer calendar, calendar booking, service scheduling, salon scheduling software
License: GPLv2 or later
Stable tag: trunk
Requires at least: 4.1
Tested up to: 5.9
Requires PHP: 5.3

Add a time slot booking option to your site and let your visitors start booking appointments with you!

== Description ==

JoeBooking is a time slot booking plugin that allows you to accept time slot bookings online.
JoeBooking time slot management allows the owner to manage the plugin through a powerful admin panel and offers a self-service customer appointment booking form embedded into a page or a post with a shortcode.

Please visit [our website](https://www.joebooking.com "WordPress Time Slot Booking") for more info and [get the Pro version now!](https://www.joebooking.com/order/).

###Pro Version Features###

__Manage Payments__
Accept online and register offline [payments](https://www.joebooking.com/payments/) for your bookings.

__Custom Fields__
[Additional fields](https://www.joebooking.com/custom-fields/) to keep custom information about your bookings.

Make sure you don't overbook yourself while keeping it easy for your potential clients to book time with you.
Switch to manage your service offerings and client appointments right from your WordPress powered website. 

== Support ==
Please contact us at https://www.joebooking.com/

Author: HitCode
Author URI: https://www.joebooking.com

== Installation ==

1. After unzipping, upload everything in the `joebooking` folder to your `/wp-content/plugins/` directory (preserving directory structure).

2. Activate the plugin through the 'Plugins' menu in WordPress.

== Upgrade Notice ==
The upgrade is simply - upload everything up again to your `/wp-content/plugins/` directory, then go to the JoeBooking menu item in the admin panel. It will automatically start the upgrade process if any needed.

== Changelog ==

= 7.0.3 = 
* BUG: Posting forms with errors resulted in a corrupted view sometimes.

= 7.0.2 = 
* Minor code optimizations.
* Added the Pro version.

= 7.0.1 = 
* Added an option for customers to see their bookings in the front end with a reference number and their name.

= 7.0.0 = 
* A major update of JoeBooking with effective minimal design, quick framework core code.

= 6.7.0 = 
* BUG: Time blocks time may be showing wrong in the admin schedule management due to daytime saving time.
* Adjust code for PHP 7 compatibility.

= 6.6.5 = 
* BUG: Security hardening added in 6.6.3 broke date range forms in admin area.
* BUG: Service details like price and duration may be hardly visible due to a wrong font color.

= 6.6.4 = 
* BUG: Security hardening added in 6.6.3 broke payment processing from Paypal and other options.

= 6.6.3 = 
* Added BFT Telecom SMS sending option.
* Clean up HTML output in the front end for links to select service/location/resource. 
* BUG: Appointment Blocks Day plugin was working incorrectly.
* Security hardening.
* Minor updates and bug fixes.

= 6.6.2 = 
* Fixed an problem with the links in the front end that appeared in the latest Firefox version update (51.0).

= 6.6.1 = 
* Removed potentially vulnerable own copy of PHPMailer library.

= 6.6.0 = 
* Added a configuration for a service that it blocks a resource, useful when you would like one service make the resource unavailable even if the availability configuration allows it.
* BUG: used coupons were still allowed in the admin panel to apply for a payment.

= 6.5.9 = 
* Minor code updates and bug fixes.

= 6.5.8 = 
* Added quick links in the customer area to show their upcoming and past appointments.
* Added Paysera payment option.
* Fixed the forgot password links.
* Minor code updates.
* BUG: A few fixes in the remote include option.
* BUG: The sequential invoice number option may have failed.
* Added a global BCC field to send copies of all automatic notifications.

= 6.5.7 = 
* BUG: Styles in the print view were enqueued incorrectly that produced a garbaged view.
* Custom fields help text can also be translated with [m][/m] tags for multilingual installs.
* Added customer custom fields in the invoice view.

= 6.5.6 = 
* In the admin area you can set if a customer has no email regardless if it's allowed on the customer side.
* BUG: The total amount of an invoice was calculated incorrectly if there were items with more than one quantity.
* BUG: The database connection may fail if there's a port setting in the MySQL details.

= 6.5.5 =
* Added Twilio SMS gateway.
* Added invoice details to Stripe payments.
* Added the "Customer Balance" plugin that shows the list of customers with their current balance.
* Added the Time Off menu in the admin panel.
* Fixed some issues when the "no email" option was enabled on the customer side.
* Move to mysqli functions for compatibility with PHP 7.
* Minor code updates and fixes.

= 6.5.4 =
* BUG: Fatal error in the admin area when editing an appointment and the attachments feature is enabled.

= 6.5.3 =
* Added the "last date" plugin that limits the last date avaiable for booking for a customer.
* Minor code updates and fixes.

= 6.5.2 =
* Bug: Custom appointment fields were not shown in the front end under certain conditions
* Added German translation

= 6.5.1 =
* Translated a few non translated strings
* The links in front end occupy entire boxes for easier tapping on mobile devices
* BUG: fixed possible infinite redirect loop in the front end booking 

= 6.5.0 =
* Added an option to download and print appointments of a specific customer.
* Added the date type for custom fields.
* Added the TextMagic SMS gateway.
* BUG: When the admin created an appointment, all promotions with coupon codes were applied even if no coupon code was given.
* BUG: The Processing Time and Finish Duration options for a service were not taking effect in appointments booked at the front end.
* BUG: The "Appointment Blocks Day" plugin did not work properly.

= 6.4.9 =
* Added date type for custom fields

= 6.4.8 =
* Now it's possible to create a promotion that does not change the price, for example if you are giving a free gift with an appointment.
* The available promotions are now displayed in the time selection for an appointment in both the admin and the customer side view.

= 6.4.7 =
* BUG: fixed the remove customer balance action when there was the money balance remaining
* When rejecting an already paid appointment, an admin is offered an option to either delete the payment or move the paid amount to the customer balance.

= 6.4.6 =
* Added permissions summary views for resources and staff views in the Salon Pro version.

= 6.4.5 =
* Minor code updates and fixes

= 6.4.4 =
* Added a check not to allow overlapping appointments for the same customer (configured in Settings > Date and Time).
* Promotions (coupons) can now be applied to packages too. [Pro]

= 6.4.3 =
* Now promotions and coupons can be associated with a specific customer. [Pro]
* Added customer id to the admin area customers list view.

= 6.4.2 =
* Optimized the iCal synchronization export which might have failed when there were many appointments (hundreds).

= 6.4.1 =
* BUG: The was a conflict with MailPoet Newsletters plugin when booking an appointment in the front end.
* Added an even action option to redirect a customer to a specified page upon successful appointment booking.
* BUG: The customers import function didn't correctly handle UTF-8 files with BOM.
* BUG: A timezone of a customer was not saved if it was allowed to book appointments without registration.

= 6.4.0 =
* BUG: The admin menu didn't work on mobile screens.
* Added a setting to hide end time in customer notification emails.
* Added a link to download the appoinment's iCal file in the admin panel.

= 6.3.9 =
* BUG: The JQuery was not properly enqueued in some WP configurations thus making some features like dropdowns and collapsing infos unavailable.
* Added the print view link in the admin appointments calendar and list view.
* Remember the columns selected for download in the admin appointments list view.

= 6.3.8 =
* BUG (really sorry): The admin main menu was not showing due to change made in version 6.3.7.

= 6.3.7 =
* BUG: The SMS menu item was not showing after 6.3.6 version update (Pro versions).
* BUG: The Customer Limits plugin was working incorrectly.
* Added an option to update the invoice due date

= 6.3.6 =
* BUG: The main admin menu was not translated when another language was activated.
* BUG: error in price view in the front end when a promotion was active.
* BUG: Packages link wasn't shown for non logged in customers.
* BUG: SQL error on service create - wrong type for "lead_in".

= 6.3.5 =
* Timezone setting is active for providers too in the control panel and notifications.
* Added a setting if the staff members can edit customers login details.
* An option to remove customer balance (after adding packages) (Pro versions).

= 6.3.4 =
* BUG: additional invoice item could not be deleted.
* BUG: bug when a package was double assigned to the customer balance if the admin/provider added a payment for them.
* BUG: Paid Through field was not being exported to the CSV download.
* Added the Coupon Code field to the appointment CSV export.

= 6.3.2 =
* BUG: some essential files were missing in the Pro versions.

= 6.3.1 =
* Added an option to add a link to appointment overview page in notification messages.
* Added an admin page to view assigned packages and delete if needed.

= 6.3.0 =
* Added an option for customers to book multiple seats if you have configured such capacity. In the availablity configuration you can set how many seats are available, as well as up to how many seats a customer can book for one appointment.
* Added an option to specify a processing time for a service, than another finish duration. The break in between is available for other appointments. For example, in a hair salon 45 minutes can be booked for a color, then set aside 30 minutes for processing (these time is available for another booking), then again 30 minutes to rinsing and styling.

= 6.2.8 =
* Added an option to make a service available in package only, i.e. a customer should purchase a package first to be able to book a service.
* Redesigned the appointment actions in the calendar view - removed the dropdown that might be not convenient on some screens.
* Added an option to sort the customer list by last name, first name, email or username.
* Added some more descriptive labels in the appointment confirmation forms on the customer side.

= 6.2.7 =
* An option to configure the summary for iCal output (for export to Google Calendar etc).

= 6.2.6 =
* The admin can now remove a coupon from an appointment.
* Added Stripe payment gateway.
* A slight JavaScript modification to avoid an issue with collapse items under some themes.
* There was an error after searching customers in the admin panel.
* Modified the search customers algorith in the admin panel to look up in more fields.

= 6.2.5 =
* An option to display in the front end if a timeslot was available, but now it's booked. First configure if you need this option in Settings > Date & Time.
* Slightly restyled the calendar and time view to show available times more distinctively.
* An option for the admin/staff to set appointment approved or pending when creating it in the back end.

= 6.2.4 =
* BUG: customer accounts related notifications (such as new customer should be approved) were sent to all backend users uncluding staff, while only admins should receive them. This applies to Salon Pro version only.

= 6.2.3 =
* Now the coupon codes can be used in the admin area for existing appointments too.
* BUG: when viewing the appointments of a customer in Customers > Appointments, appointments of all of customers were displayed
* BUG: month names were not translated if a foreign language was enabled.

= 6.2.2 =
* Pay at our office button at the final confirmation form if this option is enabled.
* The shortcode output was appearing before the page text even if it was placed after.
* A few minor optimizations.

= 6.2.1 =
* Made it compatible with Developer Mode plugin so that "Developers" are also admins in JoeBooking
* Now when downloading the appointments list in CSV (Excel) format, it's possible to select which columns are included
* BUG: the availability week wizard assigned a wrong location if you first filtered the availability by location.

= 6.2.0 =
* BUG: error in payments form for payment gateways other than Paypal
* BUG: when upgrading from older versions (4 and 5) there might be some availability timeblocks hidden in the admin area
* BUG: quite a specific bug, if you have the "filter customers" plugin enabled, and a customer purchases a package, then the notification email was sent to all admins rather than those who can see this customer.
* Calendar view preference (month, week, or next days) now is saved in the admin panel
* Multilanguage interface, as well as online edit for the text used within the applicatation
* Day view extended to display several days that have appointments or availability defined
* Admin now can generate an invoice for an appointment and send it to the customer if needed
* Appointment cart is cleared upon logout to prevent issues on shared computers
* Extended the Event Actions module, now there is a new option to add automatically add an item to a first invoice of a customer, for example a registration fee.

= 6.1.2 =
* Now it is possible to make use of full HTML including <html> and <head> tags in the message templates, previously it was allowed only in the header and the footer, for the body if the head part was used it got corrupted.
* BUG: locations, resources and services sometimes were not sorted according to settings defined in the admin area.
* BUG: a customer could not make use of balance if a package was configured to allow only a selection of services rather than all.
* BUG: if more than one appointment were booked at a time by a customer, then the payment amounts for all appointments were set as the price of the last appointment.
* Staff members now can not delete customer accounts, only administrators can do that.
* Appointment notes if any are now also displayed in the calendar and list views in the admin area.
* BUG: session handling might be conflicting with other plugins or themes

= 6.1.1 =
* Now you can add parameters for shortcode: fix_location, fix_service, fix_resource to filter these options in the front-end.
* BUG: in the customer iCal export file cancelled and no-show appointments were included. Now only approved and not completed appointments are listed.
* Admin or staff members can again give the reason for rejecting appointment, in addition now it is also stored in the appointment change history.
* A setting if to count the min advance booking period from now or from tomorrow's earliest available time.
* In the admin area now there is a filter for customers with restrictions, i.e. with Email Not Confirmed, Not Approved, Suspended to easily locate them.
* Staff members now can not completely delete appointments, only administrators can do that.
* Now invoices can be deleted in the admin area.

= 6.1.0 =
* BUG: Appointments in customer panel were not properly sorted thus appointments from one month might have appeared under another month title.
* Hide SMS configuration and logs from staff members
* BUG: SMS text message were sent even if "No Notification" checkbox was on
* BUG: "Filter customers for admin" plugin was not working
* Added appointment status legend in the customer area
* BUG: iCal and Excel (CSV) export links were not working in the customer area
* BUG: Synchronization link was not displayed to staff members
* BUG: calendar popup was shifted in position in JoeBooking admin area

= 6.0.6 =
* Added a link to customer info in the appointment dropdown in admin area
* Fixed wrong time shown for appointments in admin area when lead-out was enabled
* Minor code updates and fixes

= 6.0.5 =
* Added an option to archive a location
* Added a link to pay for existing appointments in the customer area
* Minor code updates and fixes

= 6.0.4 =
* Switched back to built-in WordPress update check functions

= 6.0.3 =
* Added the appointment creation date in the admin appointment calendar and list views as well as in CSV/Excel export file
* Added a label for internal providers in the admin appointment calendar and list views.
* Added a configuration option to pick a location or a resource randomly from available ones in the front end. So if you have multiple locations or resources, but it is not required to let customers know which one is booked, you can hide this information from them.

= 6.0.2 =
* Added an option to specify if an availability time slot is valid on odd or even weeks (or all, as before)
* Event actions or hooks feature (Premium versions).
* Minor code optimization and fixes 

= 6.0.1 =
* A slight CSS fix to escape conflicts with some WP admin themes
* Location capacity booking check fix

= 6.0.0 =
* Initial public release


Thank You.