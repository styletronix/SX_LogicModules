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
			$this->RegisterMessage($this->InstanceID, 10412);
			$this->RegisterMessage($this->InstanceID, 10413);
		
			// $arr = $this->GetListItems("actors");
			// if ($arr){
				// foreach($arr as $key1) {
					// $this->RegisterMessage($key1["InstanceID"], 10603);
				// }
			// }	
			
			$this->UpdateResult();
		}
		
		private function UpdateResult(){

		}
		
		public function MessageSink($TimeStamp, $SenderID, $Message, $Data) {
			$this->LogMessage("Message from SenderID ".$SenderID." with Message ".$Message."\r\n Data: ".print_r($Data, true), KL_DEBUG);
			
			if ($Message == 10601){
				//Variable wurde erstellt.
				$this->UpdateEvents();
			}
			
			if ($Message == 11001){
				//Link wurde erstellt.
				$this->UpdateEvents();
			}
			
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