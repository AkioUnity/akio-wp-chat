HOW TO TRANSLATE

You can already translate almost all messages appearing in your chat box (front-end)
directly from your Live Chat > Options > Messages. 

If you’d like to translate other strings (including options, chat console, etc), you will
want to follow the instructions below:


- Create new folder for the plugin like that: “wp-content/languages/lcx”
- Copy/paste that file "wp-content/plugins/screets-lcx/languages/_default.po" into your
new "lcx" folder you have just created
- Find your locale code if you don’t know:
https://wpastra.com/docs/complete-list-wordpress-locale-codes/
- Rename the file like that lcx-LOCALE_CODE.po file (i.e. lcx-fr_FR.po is for French or
lcx-pt_BR.po for Brazilian)
- Open this file with Poedit application (www.poedit.net)
- Add your translations to “Translations” part
- Upload your translations (especially mo files) into your “wp-content/languages/lcx”
folder (if “lcx” folder isn’t exists, create new one)

NOTE: Poedit should create .mo files automatically. If not, go to Poedit preferences and
ensure that “Automatically compile .mo file on save” is checked under General
settings.



MULTILINGUAL TRANSLATION (WPML AND POLYLANG)

Multilingual translation is very easy in LC just like other plugins. You should directly
use WPML or Polylang for your all languages including default one.

Translate with WPML:


- Go to your WPML > String Translations
- Select "Screets Live Chat" domain from select box
- Now you’re able to translate the messages appears in front-end

Translate with Polylang:


- Go to Languages > String translations
- Select “Screets Live Chat” group
- Now you’re able to translate the messages appears in front-end