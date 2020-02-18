# SQLite Structure

### Table `Person`
```
CREATE TABLE `Person` (
    id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
    date DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,
    name_first VARCHAR(255) NOT NULL,
    name_last VARCHAR(255) NOT NULL,
    phone VARCHAR(255) NOT NULL,
    address VARCHAR(1024) NOT NULL,
    assistance VARCHAR(255) NOT NULL,
    shutoff BOOLEAN NOT NULL,
    shutoff_date DATETIME DEFAULT NULL,
    shutoff_referredby VARCHAR(255) DEFAULT NULL,
    family VARCHAR(65535) NOT NULL,
    employed BOOLEAN NOT NULL,
    employed_location VARCHAR(255) DEFAULT NULL,
    extra TEXT DEFAULT NULL
)
```


### Table `Aid`
```
CREATE TABLE `Aid` (
    id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
    given VARCHAR(65535) NOT NULL DEFAULT "{}",
    account VARCHAR(255) NOT NULL,
    rent FLOAT NOT NULL DEFAULT 0.00,
    landlord_address VARCHAR(65536) NOT NULL,
    extra TEXT DEFAULT NULL
)
```