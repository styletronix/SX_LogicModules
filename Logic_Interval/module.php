<?
class Logic_Interval extends IPSModule {
        public function __construct($InstanceID) {
            parent::__construct($InstanceID);
 
        }
		
        public function Create() {
            parent::Create();

            $this->RegisterPropertyFloat("OnDelay", "10");
			$this->RegisterPropertyFloat("OffDelay", "10");
			$this->RegisterPropertyInteger("I_Trigger", "0");
			
			$this->RegisterVariableBoolean("Output", "Ausgang", "~Switch");

            $this->RegisterTimer("Timer_onDelay",0,'IPS_RequestAction($_IPS["TARGET"], "TimerCallback", "Timer_onDelay");');	
			$this->RegisterTimer("Timer_offDelay",0,'IPS_RequestAction($_IPS["TARGET"], "TimerCallback", "Timer_offDelay");');	
        }
		
        public function ApplyChanges() {
            parent::ApplyChanges();
				
			$this->UpdateEvents();			
			$this->UpdateInput();			
			$this->SetStatus(102);
        }
		
		private function UpdateEvents(){	
			$id = $this->ReadPropertyInteger("I_Trigger");
			if ($id > 0) {$this->RegisterMessage($id, 10603);}		
		}
		private function UpdateInput(){
			$I_TriggerID = $this->ReadPropertyInteger("I_Trigger");
				
			$I_Trigger = false;
			
			if (IPS_VariableExists($I_TriggerID)){ $I_Trigger = GetValueBoolean($I_TriggerID); }		
			
			if ($I_Trigger == true){
				$onDelay = $this->ReadPropertyFloat("OnDelay");			
				$this->SetTimerInterval("Timer_offDelay", 0);				
				$this->SetTimerInterval("Timer_onDelay", $onDelay * 1000);
				
				SetValue($this->GetIDForIdent("Output"), true);
							
			}else{
				$this->SetTimerInterval("Timer_onDelay", 0);
				$this->SetTimerInterval("Timer_offDelay", 0);
				SetValue($this->GetIDForIdent("Output"), false);
			}
		}
		public function MessageSink($TimeStamp, $SenderID, $Message, $Data) {
			if ($Message == 10603){
				$I_TriggerID = $this->ReadPropertyInteger("I_Trigger");

				if($SenderID == $I_TriggerID){
					$this->UpdateInput();
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
		private Function onTimerElapsed(string $Timer){
			$this->SetTimerInterval ($Timer, 0);
			
			switch($Timer) {
				case "Timer_onDelay":				
					SetValue($this->GetIDForIdent("Output"), false);
					
					$offDelay = $this->ReadPropertyFloat("OffDelay");	
					$this->SetTimerInterval("Timer_offDelay", $offDelay * 1000);
					break;
					
				case "Timer_offDelay":
					SetValue($this->GetIDForIdent("Output"), true);
					
					$onDelay = $this->ReadPropertyFloat("OnDelay");			
					$this->SetTimerInterval("Timer_onDelay", $onDelay * 1000);
					break;
					
				default:
					throw new Exception("Invalid Ident");

    		}
		}
    }
?>