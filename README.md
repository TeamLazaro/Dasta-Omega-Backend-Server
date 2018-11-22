
# Omega-Server

[https://github.com/TeamLazaro/Omega](https://github.com/TeamLazaro/Omega)

# todo
- [ ] Have the login page on the Omega server.
- [x] Deploy needs to account for log and db files and set permissions
- [ ] Disable the interface and show appropriate feedback when computations or fetchings are being performed
- [ ] Check if expired cookies are sent to the server
- [ ] Sort the scheduler not "stopped" bug
- Add rupee symbol to the pricing sheet
- Convert shortcodes to human readable descriptions
- Notify us of an error in the log

# setup
Install these PHP extensions,
apt-get install php7.0-mbstring
apt-get install php7.0-gd
apt-get install php7.0-xml
apt-get install php7.0-zip
apt-get install php7.0-curl

## nodeJS instance
When deploying a node app, we use PM2 as a process manager.
Go to the directory where the entry-point file of the app resides,
	pm2 start index.js --name="enquiry_processor" -i 1 --wait-ready --kill-timeout 15000

You change `index.js` and `enquiry_processor` for your use case.

# user account
On the server, When a page is requested,
	If the cookie "omega-user-v{num}" is present,
		Construct a global __OMEGA__ JavaScript var

On the client, When a page on the site is loaded,
	If a global __OMEGA__ var is present
		Fetch a fresh dump of the user's details from the server
		Trigger a "user::details" event with the user's details data

On the client, When a "user::details" event is triggered,
	plonk in the details on the page wherever desired



# flows
## OTP Verification
If cookie is present, do not show the lead form as it will have all the data you need.
If cookie is not present, show lead form.
	If number entered is in the system,
		Make a da cookie.
		Show the detailed pricing.

	If number entered ain't in the system, attempt to send an OTP and have them type it in.
		If number could not be reached, either cuz it's outta reach or don't exist,
			Give an appropriate response.
			Have them try again, possibly with another number.
		If OTP was successfully delivered and the OTP the user entered is valid,
			Make a da cookie.
			Show the detailed pricing.
		If OTP was successfully delivered and the OTP the user entered is invalid,
			Say so, and have them try again.
		If OTP was not successfully delivered,
			Give them a chance to try again.

What about fetching existing leads from the system where there was no calling code prefix?





# new system
## enquiries
An enquiry can be in one of many phases – Verify, Allocate, Negotiate and Close.
Each phase has a timeout duration following which the enquiry is auto-reassigned or brought to the attention of the manager.
Each type of saleperson user has a upper limit of enquiries per phase that they are allowed to manage.

## events
1. "Customer No Response": When a salesperson sets a "Customer No Response" status on an enquiry.
	The enquiry is re-assigned.
2. "Agent No Response": When a salesperson makes no progress or does not pursue an enquiry for the cycle time of that phase. More specifically, this involves (a) Reject-ing, (b) Setting a "Customer No Response" status, or (c) Ticking all the qualifiers
	The enquiry is re-assigned. Once re-assigned, the timeout is reset.
3. "Transfer": When a salesperson re-assigns an enquiry to another salesperon or class of salespeople.
	The enquiry is re-assigned.

## Resource allocator
Evenly distributes enquiries to all the salespeople alphabetically.
If a saleperson is saturated, then she/he is exempt from consideration.

A user who is on leave is not assigned an enquiry.


# ?
User on leave should be removed from Knowlarity.
## limits
Storage space on Zoho CRM
