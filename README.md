# SVC-People-Tracker

### Site Map
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




## table `Person`

id int
date TIMESTAMP
firstname string
lastname string
phone string
address string
assistancetype string // Convert to enumeration
shutoff/evict bool
	date if true
	referredby if true
familysize int
	familydata text
	children + age, adults, seniors
employed bool
	placeofenployment string
extradata text





## table `aid`

foodbagcount int
giftcardcount (15/26)
vouchers
	clothing
	furniture
	utilities
account #
rentcost
landlordaddress
extradata text