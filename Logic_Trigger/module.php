<?
class Logic_Trigger extends IPSModule {
        public function __construct($InstanceID) {
            parent::__construct($InstanceID);
 
        }
		
        public function Create() {
            parent::Create();

			$this->RegisterPropertyInteger("I_Trigger", "0");
			$this->RegisterPropertyBoolean("remanent", true);
			
			$this->RegisterVariableBoolean("Output", "Ausgang", "~Switch");
        }
		
        public function ApplyChanges() {
            parent::ApplyChanges();
				
			$this->UpdateEvents();	
			
			if ($this->ReadPropertyBoolean("remanent") == false){
				SetValue($this->GetIDForIdent("Output"), false);
			}
						
			$this->SetStatus(102);
        }
		
		private function UpdateEvents(){	
			$id = $this->ReadPropertyInteger("I_Trigger");
			if ($id > 0) {$this->RegisterMessage($id, 10603);}
		}
		public function MessageSink($TimeStamp, $SenderID, $Message, $Data) {
			if ($Message == 10603){
				$I_TriggerID = $this->ReadPropertyInteger("I_Trigger");
				
				$I_Trigger = false;
				
				if (IPS_VariableExists($I_TriggerID)){ $I_Trigger = GetValueBoolean($I_TriggerID); }
			
				if ($I_Trigger == true and $SenderID == $I_TriggerID){
					$val = GetValueBoolean($this->GetIDForIdent("Output"));
					SetValueBoolean($this->GetIDForIdent("Output"), ($val != true));
				}
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
    }
?>