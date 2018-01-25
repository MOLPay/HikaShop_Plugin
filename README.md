HikaShop MOLPay Payment Plugin
=====================
![alt text](https://github.com/MOLPay/Prestashop_Plugin/wiki/images/molpay-developer.jpg)
MOLPay payment plugin for HikaShop (Shopping Cart for Joomla) developed by MOLPay R&D team.


Supported version
-----------------
[Joomla 1.5 and above]

[HikaShop Starter: 3.2.1 and above]

Notes
-----

MOLPay Sdn. Bhd. is not responsible for any problems that might arise from the use of this module. 
Use at your own risk. Please backup any critical data before proceeding. For any query or 
assistance, please email support@molpay.com 


Installations
-------------

1. Download or clone this repository.

2. For seamless integration, copy [images.zip] from distribution folder, unzip it, and paste it in Joomla root folder.

3. Copy [molpay_hikashop.zip] (or [molpay_seamless_hikashop.zip]) from distribution folder.

4. Login into joomla administration panel and navigate to Extension -> Manage -> Install.

5. At field Upload Package File, Upload and Install the [molpay_hikashop.zip] (or [molpay_seamless_hikashop.zip]).

6. At the same page, click Manage and find "HikaShop MOLPay Payment Plugin" (or "HikaShop MOLPay Seamless payment plugin") from the list and ensure the status is enabled (color green).

7. Next, navigate to Components -> HikaShop. Under System menu, click on the Payment Methods link.

8. Click "New" button at the configuration menu and select "HikaShop MOLPay Payment Plugin" (or "HikaShop MOLPay Seamless payment plugin").

9. Please fill in the required fields.  
  Main information
  - Name : MOLPay

  Generic configuration
  - Published : Yes
  
10. On the Specific configuration tab, fill in the required fields.
  - MOLPay Account Type (Sandbox/Production)
  - MOLPay Merchant ID
  - MOLPay Channels (This field is required for seamless integration only)
  - MOLPay Verify (Public) Key
  - MOLPay Secret (Private) Key

11. Save the configuration and test with our sandbox account.

12. Login into MOLPay Merchant Portal and set Callback URL, Notification URL and Return URL

  ``CallbackURL: https://shoppingcarturl/index.php?option=com_hikashop&ctrl=checkout&task=notify&notif_payment=molpay&tmpl=component&lang=en`` 
  
  ``ReturnURL: https://shoppingcarturl/index.php?option=com_hikashop&ctrl=checkout&task=notify&notif_payment=molpay&tmpl=component&lang=en`` 
  
*Replace `shoppingcarturl` with your shoppingcart domain 

Contribution
------------

You can contribute to this plugin by sending the pull request to this repository.


Issues
------------

Submit issue to this repository or email to our support@molpay.com


Support
-------

Merchant Technical Support / Customer Care : support@molpay.com <br>
Sales/Reseller Enquiry : sales@molpay.com <br>
Marketing Campaign : marketing@molpay.com <br>
Channel/Partner Enquiry : channel@molpay.com <br>
Media Contact : media@molpay.com <br>
R&D and Tech-related Suggestion : technical@molpay.com <br>
Abuse Reporting : abuse@molpay.com
