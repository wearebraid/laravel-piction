## Laravel / Piction

### **NOTE: THIS IS IN BETA MODE, PLEASE DO NOT USE THIS IN A PRODUCTION SETTING**

This is a package for quick integration with Pictino for storing records in a more usable fashion.

### Features

### Installation
1. Install composer package: `composer require wearebraid/piction`
2. Install config file 
```
php artisan vendor:publish --provider="Braid\Piction\PictionServiceProvider"
```
3. Add the following items to your Laravel `.env` file:
```
PICTION_HOST=http://<your-host-here>/piction/
PICTION_USERNAME=username
PICTION_PASSWORD=password
```
4. Add service provider and facade to your app's config: `config/app.php`
```
...
'providers' => [
	...
	Wearebraid\Piction\PictionServiceProvider::class,
	...
],
'aliases' => [
	...
	'Piction' => Wearebraid\Piction\Facades\Piction::class,
	...
],
...
```

#### Basic Config Settings
##### `config/piction.php`
* `host`: This is set to pull from the `.env` file to set the host URL for your Piction install
* `user`: This is set to pull from the `.env` file to set the desired user for retrieving data from Piction
* `pass`: This is set to pull from the `.env` file to set the desired user's password for retrieving data from Piction
* `endpoint`: Default is `!soap.jsonget`, you should not need to change this as most of the functionality is relying on JSON data returned from Piction.
* `use_scout`: Default is `false` If you choose to use [Laravel's Scout](https://github.com/laravel/scout) set this to `true`. More details on using Scout and the Scout Record class below.
* `timeout`: Default is `300`, this is the number of seconds a Piction call will take before Guzzle will timeout.

---

### Using Scout _(And you should)_

Why use [Scout](https://github.com/laravel/scout)? Because it is **AWESOME!** Follow the [Scout setup](https://laravel.com/docs/master/scout#installation). Once you have it configured, in `config/piction.php` set `'use_scout' => true`

From then on, instead of using `Wearebraid\Piction\Models\Record` as your main record model, switch to `Wearebraid\Piction\Models\Scout\Record`

This model extends the normal record model adding the `Searchable` functions for indexing the records for quick searches. Then from within a controller you can then use `Record::search('spiders')->paginate(20);`

#### Command Line Scripts
1. `php artisan piction:collections` **This is the main script you will run daily** Stores latest collection info to database and adds any new collections. These records will also keep track of the last updated time for the collection. Collections that have been removed from Piction will be deleted from the database as well as all records in that collection.  This script also:
	1. Goes through each collection and requests the latest records for each.
	2. Requests deleted UMO's and removes records that no longer exist on Piction.
2. `php artisan piction:records collection_id` This command will connect to Piction and retrieve _ALL_ records since the last run for a spcific collection. A collection id is required to run this script.
3. `php artisan piction:deleted` Since Piction doesn't support webhooks, we can get the latest data each day but we don't know what's been deleted. That's where this script comes in. It connects to Piction retrieving a list of all deleted UMO's and then deletes any of those found in the database.