<?php
class LogicAndOr extends IPSModule {
        public function __construct($InstanceID) {
            parent::__construct($InstanceID);
 
        }
		
        public function Create() {
            parent::Create();

			$this->RegisterVariableBoolean("Output", "Ausgang", "~Switch");
        }
		
        public function ApplyChanges() {
            parent::ApplyChanges();
				
			$this->UpdateEvents();	
			
			$this->SetStatus(102);
        }
		
		
		public function UpdateEvents(){	
			$this->SendDebug("UpdateEvents", "", 0);
			
			$this->RegisterMessage($this->InstanceID, 10412);
			$this->RegisterMessage($this->InstanceID, 10413);
		
			$this->UpdateEventsRecursive($this->InstanceID);	
			
			$this->UpdateResult();
		}
		
		private function UpdateEventsRecursive($id){
			$this->SendDebug("UpdateEventsRecursive", "ID: ".$id, 0);
			
			foreach(IPS_GetChildrenIDs($id) as $key2) {
					$itemObject = IPS_GetObject($key2);
					$TargetID = -1;
					
					$this->SendDebug("UpdateEventsRecursive", print_r($itemObject, true), 0);
					
					if ($itemObject["ObjectType"] == 0){
						// Kategorie
						$this->RegisterMessage($key2, 10412);
						$this->RegisterMessage($key2, 10413);
						$this->UpdateEventsRecursive($key2);
					}
					
					if ($id == $this->InstanceID){
						// Do not Track changes on variables located inside the instance.
						continue;
					}
					
					if ($itemObject["ObjectType"] == 1){
						// Instanz
						$this->RegisterMessage($key2, 10412);
						$this->RegisterMessage($key2, 10413);
						// Wenn die Instanz ein Objekt mit dem Ident "Output" hat, wird dessen Wert verwendet.
						$TargetID = IPS_GetObjectIDByIdent("Output", $key2);						
					}
					
					if ($itemObject["ObjectType"] == 6){
						// Link
						$TargetID = IPS_GetLink($key2)["TargetID"];
					}
					
					if ($itemObject["ObjectType"] == 2){
						// Variable
						$TargetID = $key2;
					}
					
					if ($TargetID > 0){
						if (IPS_VariableExists($TargetID)){
							$this->RegisterMessage($TargetID, 10603);
						}	
					}
			}		
		}
		
		private function UpdateResult(){
			$this->SendDebug("UpdateResult", "Start", 0);
			
			$result = 0;
			foreach(IPS_GetChildrenIDs($this->InstanceID) as $key2) {
				$itemObject = IPS_GetObject($key2);
				if ($itemObject["ObjectType"] == 0){
					// Kategorie
					$result = $this->GetResultForGroup($key2, $itemObject["ObjectName"]);
				}
			}
			SetValue($this->GetIDForIdent("Output"), $result);
			
			$this->SendDebug("UpdateResult", $result, 0);
		}
		
		private function GetResultForGroup($id, $group){		
			$arrayResult = [];
			
			foreach(IPS_GetChildrenIDs($id) as $key2) {
				$itemObject = IPS_GetObject($key2);
				$TargetID = 0;
								
				if ($itemObject["ObjectType"] == 0){
					// Kategorie
					$val = $this->GetResultForGroup($key2, $itemObject["ObjectName"]);
					array_push($arrayResult, $val);
				}
				
				if ($itemObject["ObjectType"] == 1){
						// Instanz
						// Wenn die Instanz ein Objekt mit dem Ident "Output" hat, wird dessen Wert verwendet.
						$TargetID = IPS_GetObjectIDByIdent("Output", $key2);						
					}
				
				if ($itemObject["ObjectType"] == 6){
					// Link
					$TargetID = IPS_GetLink($key2)["TargetID"];
				}
					
				if ($itemObject["ObjectType"] == 2){
					// Variable
					$TargetID = $key2;
				}
					
				if (IPS_VariableExists($TargetID)){
					$val = GetValue($TargetID);
					array_push($arrayResult, $val);
				}
			}
			
			
	
			if ($group == "und" or $group == "and"){
				$this->SendDebug("GetResultForGroup ". $id, "Grouping: AND", 0);
				foreach($arrayResult as $val2) {
					if ($val2 == false){
						$this->SendDebug("GetResultForGroup ". $id, "False", 0);	
						return false;
					}
				}
				$this->SendDebug("GetResultForGroup ". $id, "True", 0);	
				return true;
			}
			
			if ($group == "nand"){
				$this->SendDebug("GetResultForGroup ". $id, "Grouping: NAND", 0);
				foreach($arrayResult as $val2) {
					if ($val2 == false){
						$this->SendDebug("GetResultForGroup ". $id, "True", 0);	
						return true;
					}
				}
				$this->SendDebug("GetResultForGroup ". $id, "False", 0);	
				return false;
			}
			
			
			if ($group == "oder" or $group == "or"){
				$this->SendDebug("GetResultForGroup ". $id, "Grouping: OR", 0);
				foreach($arrayResult as $val2) {
					if ($val2 == true){
						$this->SendDebug("GetResultForGroup ". $id, "True", 0);	
						return true;
					}
				}
				$this->SendDebug("GetResultForGroup ". $id, "False", 0);	
				return false;
			}
			
			if ($group == "nor"){
				$this->SendDebug("GetResultForGroup ". $id, "Grouping: NOR", 0);
				foreach($arrayResult as $val2) {
					if ($val2 == true){
						$this->SendDebug("GetResultForGroup ". $id, "False", 0);	
						return false;
					}
				}
				$this->SendDebug("GetResultForGroup ". $id, "True", 0);	
				return true;
			}
			
			if ($group == "nicht" or $group == "not"){
				$this->SendDebug("GetResultForGroup ". $id, "Grouping: NOT", 0);
				foreach($arrayResult as $val2) {
					if ($val2 == true){
						$this->SendDebug("GetResultForGroup ". $id, "False", 0);	
						return false;
					}
				}
				$this->SendDebug("GetResultForGroup ". $id, "True", 0);	
				return true;
			}
			
			if ($group == "xor"){
				$temp = false;
				
				$this->SendDebug("GetResultForGroup ". $id, "Grouping: XOR", 0);
				foreach($arrayResult as $val2) {
					if ($val2 == true){
						if ($temp == false){
							$temp = true;
						}else{
							$this->SendDebug("GetResultForGroup ". $id, "False", 0);	
							return false;
						}						
					}
				}
				$this->SendDebug("GetResultForGroup ". $id, $temp, 0);	
				return $temp;
			}
			
			$this->SendDebug("GetResultForGroup ". $id, "Grouping: UNKNOWN", 0);
		}
		
		public function MessageSink($TimeStamp, $SenderID, $Message, $Data) {
			$this->SendDebug("MessageSink ", "Message from SenderID ".$SenderID." with Message ".$Message."\r\n Data: ".print_r($Data, true), 0);
					
			if ($Message == 10412){
				//Untergeordnetes Objekt hinzugefügt.
				$this->UpdateEvents();
			}
			
			if ($Message == 10413){
				//Untergeordnetes Objekt entfernt.
				$this->UpdateEvents();
			}
			
			if ($Message == 10603){
				//Variable wurde geändert.
				$this->UpdateResult();
			}
		}		
		
		public function RequestAction($Ident, $Value) {			
    		switch($Ident) {
                case "TimerCallback":
                    $this->onTimerElapsed($Value);
                    break;

        	default:
	            throw new Exception("Invalid Ident");

    		}
 		}
		
		private Function onTimerElapsed(string $Timer){
			$this->SetTimerInterval ($Timer, 0);
			
			switch($Timer) {
				case "Timer_onDelay":						
					break;					
					
				default:
					throw new Exception("Invalid Ident");

    		}
		}
    }
?>