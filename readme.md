Twilio Calendar
========

Twilio Calendar is a simple application that allows a user to schedule Google Calendar events by calling or texting a [twilio](http://www.twilio.com) number.


twilio
------

You need a [twilio](http://www.twilio.com) account. You can sign up for a free account, though it will require that you use a pin number with every request.
An SMS might look like:

	12345678
	Dinner at Judy's house @8pm Sunday

If you make a phone call, you will need to enter this pin before you can record your message.


PHP/MySQL
------

You need to be running a web server that can run [PHP](http://www.php.net) with [MySQL](http://www.mysql.com/) installed.


Google API
------
Everything to run this app is included in lib, but for actual Google API usage, you should refer to the [documentation](http://code.google.com/apis/calendar/data/1.0/developers_guide_php.html) provided by Google.


Installation and Demo
------

Installation:

* copy files to a webserver
	
* `mv calendar.ini.sample calendar.ini`
	
* add your database information to calendar.ini
	
* create a twilio account 
	
* point SMS url to `[yourdomain]/twilio-calendar/addEvent.php`

* point Voice url to `[yourdomain]/twilio-calendar/recordCall.php` 
	
A live demo is hosted at [http://www.brendanblackwood.com/twilio-calendar](http://www.brendanblackwood.com/twilio-calendar)


Disclaimer
------
Right now, everything is stored in plain text. That means that I'm taking no precautions with your precious, precious google data. I can promise that I won't steal your passwords for anything (or even look at them), but from a security standpoint, I recommend using a test account. I'm planning on implementing Google's AuthSub authentication, but I haven't done so yet.