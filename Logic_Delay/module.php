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
            parent::ApplyChanges();
			
			$this->SetBuffer("I_Trigger_Status", false);
			
			$this->UpdateEvents();
			$this->SetStatus(102);
        }
		
		public function UpdateEvents(){	
			$id = $this->ReadPropertyInteger("I_Trigger");
			if ($id > 0) {$this->RegisterMessage($id, 10603);}
			
			$id = $this->ReadPropertyInteger("I_Reset");
			if ($id > 0) {$this->RegisterMessage($id, 10603);}
		}
		public function MessageSink($TimeStamp, $SenderID, $Message, $Data) {
			if ($Message == 10603){
				$I_TriggerID = $this->ReadPropertyInteger("I_Trigger");
				$I_ResetID = $this->ReadPropertyInteger("I_Reset");
				
				$I_Trigger = GetValueBoolean($I_TriggerID);
				$I_Reset = GetValueBoolean($I_ResetID);
								
				if($SenderID == $I_TriggerID and $I_Reset == false){
					if ($I_Trigger == true){
						if ($this->GetBuffer("Status") == "off"){
							$this->SetBuffer("Status", "onDelay");
							$this->SetTimerInterval("Timer_offDelay", 0);
							$this->SetTimerInterval("Timer_onDelay", ($this->ReadPropertyFloat("OnDelay") * 1000) + 1);
						}					
					}else{
						if ($this->GetBuffer("Status") != "offDelay"){
							$this->SetBuffer("Status", "offDelay");
							$this->SetTimerInterval("Timer_onDelay", 0);						
							$this->SetTimerInterval("Timer_offDelay", ($this->ReadPropertyFloat("OffDelay") * 1000) + 1);	
						}										
					}
				}
				
				if($SenderID == $I_ResetID){
					if ($I_Reset == true){
						$this->SetBuffer("Status", "off");
						$this->SetTimerInterval("Timer_offDelay", 0);
						$this->SetTimerInterval("Timer_onDelay", 0);
						SetValue($this->GetIDForIdent("Output"), false);
					}else if ($I_Trigger == true){
						if ($this->GetBuffer("Status") == "off"){
							$this->SetBuffer("Status", "onDelay");
							$this->SetTimerInterval("Timer_offDelay", 0);
							$this->SetTimerInterval("Timer_onDelay", ($this->ReadPropertyFloat("OnDelay") * 1000) + 1);
						}	
					}					
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
					$this->SetBuffer("Status", "on");
					SetValue($this->GetIDForIdent("Output"), true);
					break;
					
				case "Timer_offDelay":
					$this->SetBuffer("Status", "off");
					SetValue($this->GetIDForIdent("Output"), false);
					break;
					
				default:
					throw new Exception("Invalid Ident");

    		}
		}
    }
?>