HikaShop MOLPay Payment Plugin
=====================

MOLPay payment plugin for HikaShop (Shopping Cart for Joomla) developed by MOLPay R&D team.


Supported version
-----------------
[Joomla 1.5 above]

Notes
-----

MOLPay Sdn. Bhd. is not responsible for any problems that might arise from the use of this module. 
Use at your own risk. Please backup any critical data before proceeding. For any query or 
assistance, please email support@molpay.com 


Installations
-------------

1. Download or clone this repository.

2. Copy [molpay_hikashop.zip] from distribution folder.

3. Login into joomla administration panel and navigate to Extension -> Manage -> Install.

4. At field Upload Package File, Upload and Install the [molpay_hikashop.zip].

5. At the same page, click Manage and find "HikaShop MOLPay Payment Plugin" from the list and ensure the status is enabled (color green).

6. Next, navigate to Components -> HikaShop. Under System menu, click on the Payment Methods link.

7. Click "New" button at the configuration menu and select "HikaShop MOLPay Payment Plugin".

8. Please fill the required fields.  
  Main information
  - Name : MOLPay

  Generic configuration
  - Published : Yes
  
9. On the Specific configuration tab, fill the required fields.
  - MOLPay Merchant ID
  - MOLPay Verify Key
  - MOLPay Private Key (If this field is left blank, it will be the same as Verify Key)

10. Save the configuration and test with our sandbox account.

11. Login into MOLPay Merchant Admin and set Callback URL and Return URL

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
