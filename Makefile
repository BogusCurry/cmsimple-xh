# $Id$

PHPSOURCES=cmsimple/adminfuncs.php\
	   cmsimple/cms.php\
	   cmsimple/functions.php\
	   cmsimple/tplfuncs.php\
	   cmsimple/classes/CSRFProtection.php\
	   cmsimple/classes/FileEdit.php\
	   cmsimple/classes/LinkCheck.php\
	   cmsimple/classes/Mailform.php\
	   cmsimple/classes/page_data_model.php\
	   cmsimple/classes/page_data_router.php\
	   cmsimple/classes/page_data_views.php\
	   cmsimple/classes/Pages.php\
	   cmsimple/classes/Search.php\
	   plugins/meta_tags/index.php\
	   plugins/meta_tags/_admin.php\
	   plugins/meta_tags/meta_tags_view.php\
	   plugins/filebrowser/index.php\
	   plugins/filebrowser/admin.php\
	   plugins/filebrowser/editorbrowser.php\
	   plugins/filebrowser/classes/filebrowser.php\
	   plugins/filebrowser/classes/filebrowser_view.php\
	   plugins/filebrowser/classes/required_classes.php

TUTORIALS=tutorials/XH/CSRFProtection.cls

EMPTY=
SPACE=$(EMPTY) $(EMPTY)
COMMA=,

.PHONY: tests
tests: check-phpunit check-cmsimpledir
	cd tests/; $(PHPUNIT) --bootstrap bootstrap.php --colors .; cd ..

.PHONY: coverage
coverage: check-phpunit
	cd tests/; $(PHPUNIT) --bootstrap bootstrap.php --coverage-html coverage/ .; cd ..

.PHONY: doc
doc: doc/php/index.html

doc/php/index.html: check-phpdoc $(PHPSOURCES) $(TUTORIALS)
	$(PHPDOC) --filename $(subst $(SPACE),$(COMMA),$(PHPSOURCES) $(TUTORIALS))\
		  --target doc/php\
		  --defaultcategoryname CMSimple_XH\
		  --defaultpackagename XH

.PHONY: sniff
sniff: check-phpcs
	$(PHPCS) $(PHPSOURCES)

.PHONY: phpci
phpci: check-phpci
	$(PHPCI) --dir cmsimple

.PHONY: check-phpunit check-phpdoc check-phpcs check-phpci check-cmsimpledir
check-phpunit:
	if test "$(PHPUNIT)" = "" ; then echo "PHPUNIT not set"; exit 1; fi
check-phpdoc:
	if test "$(PHPDOC)" = "" ; then echo "PHPDOC not set"; exit 1; fi
check-phpcs:
	if test "$(PHPCS)" = "" ; then echo "PHPCS not set"; exit 1; fi
check-phpci:
	if test "$(PHPCI)" = "" ; then echo "PHPCI not set"; exit 1; fi
check-cmsimpledir:
	if test "$(CMSIMPLEDIR)" = "" ; then echo "CMSIMPLEDIR not set"; exit 1; fi
