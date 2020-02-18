# SVC-People-Tracker

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
            => reportGenerate.twig

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
```

