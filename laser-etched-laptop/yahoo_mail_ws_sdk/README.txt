Yahoo! Mail Web Service SDK (prerelease)
----------------------------------------
Welcome to the exciting world of Yahoo! Mail. This file should get you 
well on your way to hacking with the new Yahoo! Mail Web Service. Bear in 
mind, just as the web service is a prerelease, so is this SDK. If you find 
issues in the SDK, don't hesitate to report them to the Yahoo! Group:

        http://tech.groups.yahoo.com/group/ydn-mail/

Getting Started
---------------
You will need a few things to get started:

   * This SDK
   * Your own hosted domain
   * A yahoo.com account

Follow the directions to sign up an application with developer.yahoo.com:

        http://developer.yahoo.com/auth/appreg.html

When it comes time to fill in the "URL" field, it should point to wherever 
you have the included "token.php" installed. Also, make sure you request 
read/write access to Yahoo! Mail.

You will be given two pieces of information: a secret and an application 
ID. Open up "lib/config.inc" and modify the variables within. You will 
have to change $SECRET, $APP_ID, $HOSTNAME and $BASE_URL.

Testing the Install
-------------------
Point your web browser to http://yourdomain.com/yourpath/index.php. You 
should be asked to log in. Click the link and log in at yahoo.com. You 
will be asked to agree to a terms of service, Once you've agreed to that, 
you will be redirected back to your application where you should see 
your folder list as well as a message list.

Help
----
There are Yahoo! Mail web service engineers at hack day. We should have a 
booth on the second floor of building C (up near where the talks are taking 
place). If you can't find us, try the help desk or send an email:

    rckenned@yahoo-inc.com

Good luck hacking!
