Blackshot Player [Database] [PHP]
==================

A quick PHP file to retreive information of a particular user on Blackshot.


## Example

``` php
// Create a new Blackshot class for querying
$player = new Blackshot()

// Set user id
$player->setID(111111111);

// And get data and parse them
$player->getData();
$data = $player->parseData();

// Print out data
print_r($data);

```

## ToDo
- [ ] Player class
- [ ] Get shot rate on body parts
