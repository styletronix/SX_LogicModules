<?php
class Logic_AndOr extends IPSModule {
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
		
		private function UpdateEvents(){	
			$this->LogMessage("UpdateEvents", KL_MESSAGE);
			
			$this->RegisterMessage($this->InstanceID, 10412);
			$this->RegisterMessage($this->InstanceID, 10413);
		
			$this->UpdateEventsRecursive($this->InstanceID);	
			
			$this->UpdateResult();
		}
		
		private function UpdateEventsRecursive($id){
			foreach(IPS_GetChildrenIDs($id) as $key2) {
					$itemObject = IPS_GetObject($key2);
					$TargetID = 0;
					
					if ($itemObject["ObjectType"] == 0){
						// Kategorie
						$this->RegisterMessage($TargetID, 10412);
						$this->RegisterMessage($TargetID, 10413);
						$this->UpdateEventsRecursive($TargetID);
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
						$this->RegisterMessage($TargetID, 10603);
					}
			}		
		}
		
		private function UpdateResult(){
			$this->LogMessage("UpdateResult", KL_MESSAGE);
			
			$result = 0;
			foreach(IPS_GetChildrenIDs($this->InstanceID) as $key2) {
				$itemObject = IPS_GetObject($key2);
				if ($itemObject["ObjectType"] == 0){
					// Kategorie
					$result = $this->GetResultForGroup($key2, $itemObject["ObjectName"])
				}
			}
			SetValue($this->GetIDForIdent("Output"), $result);
			
			$this->LogMessage("UpdateResult: ".$result, KL_MESSAGE);
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
				foreach($arrayResult as $val2) {
					if ($val2 == false){
						return false;
					}
				}
				return true;
			}
			
			if ($group == "oder" or $group == "or"){
				foreach($arrayResult as $val2) {
					if ($val2 == true){
						return true;
					}
				}
				return false;
			}
		}
		
		public function MessageSink($TimeStamp, $SenderID, $Message, $Data) {
			$this->LogMessage("Message from SenderID ".$SenderID." with Message ".$Message."\r\n Data: ".print_r($Data, true), KL_MESSAGE);
					
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
			$this->LogMessage("UpdateResult", KL_MESSAGE);
			
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