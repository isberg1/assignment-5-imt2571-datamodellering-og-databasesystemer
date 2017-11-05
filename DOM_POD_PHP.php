<?php

//////////////////////////////////////////////////////////////////////

//note: the PHPmyAdmim database must be empty before the skript is run

//////////////////////////////////////////////////////////////////////

//DOM setup
$Doc = new DOMDocument();
$Doc->load("SkierLogs.xml");
$Doc->normalize();
$xpath = new DOMXpath($Doc);

//data base setup
$dbHost = 'localhost';
$dbName = 'oblig5';
$dbUser = 'root';
$dbPwd = '';

try {
    // Create PDO connection
    $db = new PDO("mysql:host=$dbHost;dbname=$dbName;",
                  "$dbUser", "$dbPwd", 
                  array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
                        
						
 
 echo "\n db_ conect success \n"; //raport success

	
} catch (PDOException $excpt) {	 //deal with errors
    echo $excpt->getMessage(); 	//print error message

}

 echo "\n";
 

 //////////////////////////////////////////////////////////////////////
  //get Club data from XML and send data to database
  
 $nodes = $Doc->getElementsByTagName('Club');		//get club nodes
 $nodeLength=$nodes->length;						//find length of club nodes array
 $childLength=($nodes->item(1)->childNodes->length);//find standard length of club nodes child array(the rigth type of child node)	
	
	try
	{
		// Prepared INSERT statement 
		$stmtClubs = $db->prepare(
                   "INSERT INTO clubs (clubId, clubName, city,county) "
                 . "VALUES(:clubId,:clubName,:city,:county)");
	
	
		for($i=0; $i<$nodeLength; $i++)	//loop club attribute value
		{		
			$club=$nodes->item($i)->attributes->getNamedItem("id")->nodeValue;	// get attribute valu
		
			for($j=0; $j<$childLength; $j++) //loop club child array for node data
			{
				$a[$j] = $nodes->item($i)->childNodes->item($j)->nodeValue;	 //get club child node data			
			}
			// Prepared INSERT statement.   the relevant values are allways in position 1,3,5
			$stmtClubs->execute(array(':clubId' => "$club" ,':clubName' => "$a[1]", ':city'=>"$a[3]",':county'=>"$a[5]"));	
		}	  
    
		echo "<p>clubs insert_success</p>";    //raport success
	
	} catch (PDOException $excpt) { //deal with errors
    echo $excpt->getMessage(); 	//print error message

	}
	echo "\n";	
	
	
 //////////////////////////////////////////////////////////////////////	
//get Skier data from XML and send data to database

		
	// Prepared INSERT statement 
    $stmt = $db->prepare(
                   "INSERT INTO skiers (userName, firstName, lastName, yearOfBirth) "
                 . "VALUES(:userName, :firstName, :lastName, :yearOfBirth)");    
    	
	try
	{
		($firstNameNode = $Doc->getElementsByTagName('FirstName'));     //get first name node array
		($lastNameNode= $Doc->getElementsByTagName('LastName'));        //get last name node array
		($yearOfbirthNode= $Doc->getElementsByTagName('YearOfBirth'));  //get birth year node array
		
		}catch(PDOException $l)  //deal with errors
		{echo $l->getMessage();} //print error message
		
		try{
			for($nn =0; $nn < $firstNameNode->length; $nn++)// loop first name node array
			{
				// get the correct valus for each skier
				$skierNode=$firstNameNode->item($nn)->parentNode->attributes->getNamedItem("userName")->value;
				$fName=$firstNameNode->item($nn)->nodeValue;
				$lName=$lastNameNode->item($nn)->nodeValue;
				$years=$yearOfbirthNode->item($nn)->nodeValue;	
					// execute INSERT statement 
				$stmt->execute(array(':userName' => "$skierNode", ':firstName' => "$fName",':lastName' => "$lName", ':yearOfBirth' => "$years"));			
			}	
		echo "<p>Inserted successful for skiers</p>"; //raport success
		
	}catch(PDOException $g) 	 //deal with errors
		{echo $g->getMessage();} //print error message
		
	echo "\n";	
		
		
 //////////////////////////////////////////////////////////////////////	
//get Distance data	from XML and send data to database
		
	$seasonNode=$Doc->getElementsByTagName('Season'); //get season node
	//storage arrays
	$fallYearNode= array();	
	$clubNode=array();
	$userNameNode=array();
		
	try{
	// Prepared INSERT statement 
    $stmtSeason = $db->prepare(
                   "INSERT INTO season (userName, fallYear, totalDistance, clubs) "
                 . "VALUES(:userName, :fallYear, :totalDistance, :clubs)");							
	
	for($ww =0; $ww < $seasonNode->length; $ww++)// loop Season for season attribute
	{
		$skiers=$seasonNode->item($ww)->childNodes;//move down the tree one step
		if($seasonNode->item($ww)->hasAttributes()) //check if node as an attribute
		{
			$fallYearNode[$ww]=$seasonNode->item($ww)->attributes->getNamedItem("fallYear")->nodeValue;  //get fallYear
			
			for($gg =0; $gg<$skiers->length; $gg++)// loop Season/skiers for Clubs attribute
			{
				if($skiers->item($gg)->nodeType == 1)//check for right type of node
				{	if($skiers->item($gg)->hasAttributes())	//if skier is a member of a club
					{	
						($clubNode[$gg]=$skiers->item($gg)->attributes->getNamedItem("clubId")->nodeValue);  //get clubID
					}
					else	//if skier is not a member of a club
					{$clubNode[$gg]= null;}	

					$skierNameNode = $skiers->item($gg)->childNodes; //move down the tree one step
					
					for($hh =0; $hh < $skierNameNode->length; $hh++)// loop Season/skiers/skier for userName
					{
						if($skierNameNode->item($hh)->hasAttributes() and $skierNameNode->item($hh)->nodeType == 1)//check for right type of node and attribute
						{
							($userNameNode[$hh]= $skierNameNode->item($hh)->attributes->getNamedItem("userName")->nodeValue); //get userName							
							
							($entityNode=$skierNameNode->item($hh)->childNodes->item(1)->childNodes);	//move down the tree one step						
							
							$distance=0;	//reset distance valu to 0 
							for($mm=0; $mm<$entityNode->length; $mm++) // loop Season/skiers/skier for distance nodes 
							{
								if($entityNode->item($mm)->nodeType == 1) //check for right type of node
								{
									($distance+=$entityNode->item($mm)->childNodes->item(5)->nodeValue); //add up all distance node	vlues						
								}								
							}							
							
							if($clubNode[$gg]) // if $clubNode[$gg] is not Null
								// execute INSERT statement 
							{$stmtSeason->execute(array(':userName' => "$userNameNode[$hh]", ':fallYear' => "$fallYearNode[$ww]",':totalDistance' => "$distance", ':clubs' => "$clubNode[$gg]"));}
							else// if $clubNode[$gg] is Null
								// execute INSERT statement 
							{$stmtSeason->execute(array(':userName' => "$userNameNode[$hh]", ':fallYear' => "$fallYearNode[$ww]",':totalDistance' => "$distance", ':clubs' => null));}
														
						}		
					}
				}			
			}
		}		
	}   echo "<p>Inserted success for season</p>"; //raport success
	
	
	}catch(PDOException $k)			//deal with errors
		{echo $k->getMessage(); }	//print error message

	echo "\n\n skript done";	
?>

