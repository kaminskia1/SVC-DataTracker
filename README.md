# SVC People Tracker

[![Updated Badge](https://img.shields.io/github/last-commit/kaminskia1/SVC-People-Tracker)](https://github.com/kaminskia1/SVC-People-Tracker/)
[![Release](https://badges.pufler.dev/created/kaminskia1/SVC-People-Tracker)](https://github.com/kaminskia1/SVC-People-Tracker/)
[![License](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)

### Abstract

##### TODO: this

### Requirements
	- Composer (>= 1.9.3) [Pre-packaged in release]
	- PHP (>=7.1.3) [Pre-packaged in release]
	- PHPDesktop (>=57.0) [Pre-packaged in release]
	- jQuery (>=3.4.1) [Pre-packaged in /src/assets/js for offline usage]
	- FontAwesome (>=4.7.0) [Pre-packaged in /src/assets/css for offline usage]
	
### Site Map
```
/index.php
    => GET
        => dashboard.twig
        => personList.twig
    	    => personView.twig

        => personAdd.twig
        => aidView.twig
        => aidAdd.twig
        => reportList.twig
        => reportAdd.twig

    => POST
        => connection
        => template
	    => dashboard.twig
	    => personList.twig
	        => personView.twig

	    => personAdd.twig
	    => aidView.twig
	    => aidAdd.twig
	    => reportList.twig
	    => reportAdd.twig

        => push
	    => personAdd
	    => personUpdate
	    => personDelete
	    => aidAdd
	    => aidUpdate
	    => aidRemove
	    => reportAdd
	    => reportRemove
```




### Frontend Navigation Map
###### Public Name => Internal Callback
```
Main
	Dashboard => dashboard

People
	View People => personList
	Add Person => personAdd
		View Person => personView

Aid
	View Aid => aidList
	Add Aid => aidAdd
		View Entry => aidView

Report
	View Reports => reportList
	Generate Report => reportGenerate
``
