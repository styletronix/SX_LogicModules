<?
class Logic_Delay extends IPSModule {
        public function __construct($InstanceID) {
            parent::__construct($InstanceID);
 
        }
		
        public function Create() {
            parent::Create();

            $this->RegisterPropertyFloat("OnDelay", "0");
			$this->RegisterPropertyFloat("OffDelay", "0");
			$this->RegisterPropertyInteger("I_Trigger", "0");
			$this->RegisterPropertyInteger("I_Reset", "0");
			
			$this->RegisterVariableBoolean("Output", "Ausgang", "~Switch");

            $this->RegisterTimer("Timer_onDelay",0,'IPS_RequestAction($_IPS["TARGET"], "TimerCallback", "Timer_onDelay");');	
			$this->RegisterTimer("Timer_offDelay",0,'IPS_RequestAction($_IPS["TARGET"], "TimerCallback", "Timer_offDelay");');	
			
			$this->SetBuffer("Status", "off");
        }
		
        public function ApplyChanges() {
			$I_TriggerID = $this->ReadPropertyInteger("I_Trigger");
			if ($I_TriggerID !> 0){ 
				$I_TriggerID = IPS_GetParent($_IPS['SELF']); 
				if (IPS_VariableExists($I_TriggerID)){
					IPS_SetProperty($_IPS['SELF'], "I_Trigger", $I_TriggerID);
				}
			}	
			
            parent::ApplyChanges();
				
			$this->UpdateEvents();			
			$this->UpdateInput();			
			$this->SetStatus(102);
        }
		
		private function UpdateEvents(){	
			$id = $this->ReadPropertyInteger("I_Trigger");
			if ($id > 0) {$this->RegisterMessage($id, 10603);}
			
			$id = $this->ReadPropertyInteger("I_Reset");
			if ($id > 0) {$this->RegisterMessage($id, 10603);}			
		}
		private function UpdateInput(){
			$I_TriggerID = $this->ReadPropertyInteger("I_Trigger");
			$I_ResetID = $this->ReadPropertyInteger("I_Reset");
				
			$I_Trigger = false;
			$I_Reset = false;
			
			if (IPS_VariableExists($I_TriggerID)){ $I_Trigger = GetValueBoolean($I_TriggerID); }
			if (IPS_VariableExists($I_ResetID)){ $I_Reset = GetValueBoolean($I_ResetID); }			
			
			if ($I_Reset == true){
				$this->SetTimerInterval("Timer_offDelay", 0);
				$this->SetTimerInterval("Timer_onDelay", 0);
				SetValue($this->GetIDForIdent("Output"), false);
				
			}else if ($I_Trigger == true){
				$this->SetTimerInterval("Timer_offDelay", 0);
				
				$onDelay = $this->ReadPropertyFloat("OnDelay");
				if ($onDelay > 0){
					$this->SetTimerInterval("Timer_onDelay", $onDelay * 1000);
				}else{
					SetValue($this->GetIDForIdent("Output"), true);
				}			
			}else if ($I_Trigger == false){
				$this->SetTimerInterval("Timer_onDelay", 0);
				
				$offDelay = $this->ReadPropertyFloat("OffDelay");
				if ($offDelay > 0){
					$this->SetTimerInterval("Timer_offDelay", $offDelay * 1000);
				}else{
					SetValue($this->GetIDForIdent("Output"), false);
				}
			}
		}
		public function MessageSink($TimeStamp, $SenderID, $Message, $Data) {
			if ($Message == 10603){
				$I_TriggerID = $this->ReadPropertyInteger("I_Trigger");
				$I_ResetID = $this->ReadPropertyInteger("I_Reset");
				
				if($SenderID == $I_TriggerID or $SenderID == $I_ResetID){
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
					SetValue($this->GetIDForIdent("Output"), true);
					break;
					
				case "Timer_offDelay":
					SetValue($this->GetIDForIdent("Output"), false);
					break;
					
				default:
					throw new Exception("Invalid Ident");

    		}
		}
    }
?>