all: info

SELENIUM_ARGS="-Dbrowser=firefox"

refresh: clean init

refresh-all: clean-all init-all

init:
	@echo "Setup website resources..."
	@python services/init.py website
	@echo ""

init-all: init
	@echo "Setup development tools..."
	@python services/init.py tools
	@echo ""

clean:
	@echo "Cleanup website resources..."
	@python services/cleanup.py website
	@echo ""

clean-all: clean
	@echo "Cleanup development tools..."
	@python services/cleanup.py tools
	@echo ""

generate-language-pack:
	@echo "Generate Pootle language pack..."
	@python services/pootle.py generate
	@echo ""

init-backup:
	@echo "Setup Backup Service..."
	@python services/init.py backup
	@echo ""

deploy-test:
	@echo "Deploy website to catroidwebtest..."
	@python services/deploy.py webtest
	@echo ""

run-tests: run-checker-css run-checker-js run-checker-deadlinks run-phpunit-tests run-selenium-tests

run-checker-css:
	@echo "Run CSS Checker..."
	@python services/tests.py css
	@echo ""

run-checker-js:
	@echo "Run Javascript Checker..."
	@python services/tests.py js
	@echo ""

run-checker-deadlinks:
	@echo "Run Deadlink Checker..."
	@python services/tests.py deadlinks
	@echo ""

run-phpunit-tests: run-phpunit-framework-tests run-phpunit-catroid-tests run-phpunit-admin-tests run-phpunit-api-tests run-phpunit-common-tests

run-phpunit-framework-tests:
	@echo "Run PHPUnit Framework Tests..."
	@python services/tests.py phpunit framework
	@echo ""

run-phpunit-catroid-tests:
	@echo "Run PHPUnit Catroid Tests..."
	@python services/tests.py phpunit catroid
	@echo ""

run-phpunit-admin-tests:
	@echo "Run PHPUnit Admin Tests..."
	@python services/tests.py phpunit admin
	@echo ""

run-phpunit-api-tests:
	@echo "Run PHPUnit API Tests..."
	@python services/tests.py phpunit api
	@echo ""

run-phpunit-common-tests:
	@echo "Run PHPUnit Common Tests..."
	@python services/tests.py phpunit common
	@echo ""

run-selenium-tests:
	@echo "Run Selenium Tests..."
	@python services/tests.py selenium catroid "$(SELENIUM_ARGS)"
	@echo ""

run-selenium-single-test:
	@echo "Run Selenium Single Test..."
	@python services/tests.py selenium single "$(SELENIUM_ARGS)"
	@echo ""

run-selenium-group-test:
	@echo "Run Selenium Group Test..."
	@python services/tests.py selenium group "$(SELENIUM_ARGS)"
	@echo ""

stop-selenium:
	@echo "Stop Selenium Tools..."
	@python services/tests.py selenium stop
	@echo ""


info:
	@echo "Usage: Basic commands to get along with the Catroweb project."
	@echo ""
	@echo "This file helps you to initialize and update your database and tools, "
	@echo "running tests on your work and deploy it the webserver."
	@echo ""
	@echo "Options:"
	@echo "  refresh               Refreshes the website environment."
	@echo "  refresh-all           Refreshes the website environment and updates the tools."
	@echo "  init                  Initializes the website environment (database, "
	@echo "                        folder structure)"
	@echo "  init-all              Initializes the website environment and the tools."
	@echo "  clean                 Cleans website related resources."
	@echo "  clean-all             Cleans website related resources and the tools."
	@echo "  generate-language-pack"
	@echo "                        Generates a pootle language pack."
	@echo "  run-tests             Runs all available tests."
	@echo "  run-checker-css       Performs some static checks on your CSS, its most basic"
	@echo "                        function is to ensure that your CSS parses."
	@echo "  run-checker-js        Runs tests on javascript files."
	@echo "  run-checker-deadlinks Runs deadlink checker."
	@echo "  run-phpunit-tests     Runs all PHPUnit tests."
	@echo "  run-phpunit-framework-tests"
	@echo "                        Runs the PHPUnit Framework testsuite."
	@echo "  run-phpunit-catroid-tests"
	@echo "                        Runs the PHPUnit Catroid testsuite."
	@echo "  run-phpunit-admin-tests"
	@echo "                        Runs the PHPUnit Admin testsuite."
	@echo "  run-phpunit-api-tests"
	@echo "                        Runs the PHPUnit API testsuite."
	@echo "  run-phpunit-common-tests"
	@echo "                        Runs the PHPUnit Common testsuite."
	@echo "  run-selenium-tests    Runs the complete Selenium testsuite."
	@echo "                        (default browser: Firefox)"
	@echo "  run-selenium-single-test"
	@echo "                        Runs a Selenium single test, test class and methods are"
	@echo "                        asked if not provided."
	@echo "  run-selenium-group-test"
	@echo "                        Runs a Selenium group test, test group is asked if not"
	@echo "                        provided."
	@echo "  stop-selenium         Stops all running selenium tools."
	@echo "  info                  Shows the available options."
	@echo ""

