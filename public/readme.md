
dump composer configs 
composer dump-autoload 

QR Code 
https://www.binaryboxtuts.com/php-tutorials/symfony-tutorials/symfony-5-qr-code-generator-tutorial/

//Remove image background
https://www.remove.bg/upload

progress bar
https://codepen.io/bold02/pen/XWKMXNe

php bin/console debug:router
 //Regen Entities
//  https://ourcodeworld.com/articles/read/1386/how-to-generate-the-entities-from-a-database-and-create-the-crud-automatically-in-symfony-5

// 	Auto create Entities
//   php bin/console doctrine:mapping:import "App\Entity" annotation --path=src/Entity

//	 generate the getters and setters of the entities
//   php bin/console make:entity --regenerate App

//	Make CRUD and create Twig Templates
//   php bin/console make:crud 

//  @ORM\Entity(repositoryClass="App\Repository\MeaningVotesRepository")

* @ORM\Entity(repositoryClass="App\Repository\CustomerRepository")
* @ORM\Entity(repositoryClass="App\Repository\OtpRepository")
* @ORM\Entity(repositoryClass="App\Repository\ReservationRepository")
* @ORM\Entity(repositoryClass="App\Repository\PlaceRepository")
* @ORM\Entity(repositoryClass="App\Repository\WaitqueueRepository")
* @ORM\Entity(repositoryClass="App\Repository\PlaceHolidaysRepository")
* @ORM\Entity(repositoryClass="App\Repository\PlaceQueueRepository")
* @ORM\Entity(repositoryClass="App\Repository\PlaceScheduleRepository")
* @ORM\Entity(repositoryClass="App\Repository\PlaceUserRepository")


 public function jsonSerialize()
    {
        return get_object_vars($this);
    }

	implements JsonSerializable
	
// https://ourcodeworld.com/articles/read/1314/how-to-automatically-generate-the-doctrine-repository-class-of-an-entity-in-symfony-5

//Start Server
// symfony server:start

//Symfony CLI Update
brew install symfony-cli/tap/symfony-cli

//Install Caching
 //https://symfonycasts.com/screencast/symfony-fundamentals/cache-service
    
//php bin/console debug:autowiring cache
//Clear Cache 
//php bin/console cache:clear           

//Doctrine Suppor for Enums, readonly or virtual columns
  https://www.doctrine-project.org/2022/01/11/orm-2.11.html
//   Native SQL Query 
//   https://www.doctrine-project.org/projects/doctrine-orm/en/2.13/reference/native-sql.html 

JSON Queries 
https://www.slideshare.net/DagHWanvik/json-array-indexes-in-mysql-226743659

//Doctrine Custom Queries
//https://www.doctrine-project.org/projects/doctrine-orm/en/2.13/reference/native-sql.html
        
//Twig Page properties
//https://stackoverflow.com/questions/19852637/set-variable-in-parent-scope-in-twig
//https://stackoverflow.com/questions/32764776/accessing-a-variable-set-in-a-child-template-from-the-parent-template-with-twig

ALTER TABLE 	`c_words` 
	DROP KEY `IX_SourceID`,
	DROP COLUMN `Sources` ;
    
ALTER TABLE `c_words` 
 ADD COLUMN  `Sources` json GENERATED ALWAYS AS (json_extract(`Data`,_utf8mb4'$.Meanings[*].Source.SourceId')) STORED,
 ADD KEY `IX_SourceID` ((cast(json_unquote(json_extract(`Sources`,_utf8mb4'$')) as unsigned array)));

 web.config 
 https://gist.github.com/RenanLazarotto/2d851f8fe0a9aa98debff7058590c76e
 
 Phone experience ... 
 https://www.twilio.com/blog/international-phone-number-input-html-javascript
 https://intl-tel-input.com/ 
 

jQuery GUI Grid
https://generic-ui.com/examples/users

jsGrid 
http://js-grid.com/demos/

BlueImp Image Loader
https://github.com/blueimp/JavaScript-Load-Image

jsWizard 
http://techlaboratory.net/jquery-smartwizard

jqFlipSwitch
https://korilakkuma.github.io/jqFlipSwitch/

FlipClock
https://github.com/gokercebeci/flipclock

Hour glass:
https://codepen.io/kleazenbee/pen/vWVyYV

Count up timer 
https://stackoverflow.com/questions/59338180/count-up-timer-with-jquery 

Hourglass
https://codepen.io/kleazenbee/pen/vWVyYV 

jquery confirm 
https://craftpip.github.io/jquery-confirm/ 

Theme 
<!--
    SOFTY PINKO
    https://templatemo.com/tm-535-softy-pinko
    -->
