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
        }
		
        public function ApplyChanges() {
            parent::ApplyChanges();
			
			$this->SetBuffer("I_Trigger_Status", false);
			
			$this->UpdateEvents();
			$this->SetStatus(102);
        }
		
		public function UpdateEvents(){	
			$id = $this->ReadPropertyInteger("I_Trigger");
			if ($id > 0) {$this->RegisterMessage(, 10603);}
			
			$id = $this->ReadPropertyInteger("I_Reset");
			if ($id > 0) {$this->RegisterMessage(, 10603);}
		}
		public function MessageSink($TimeStamp, $SenderID, $Message, $Data) {
			if ($Message == 10603){
				if($SenderID == $this->ReadPropertyInteger("I_Trigger")){
					$val = GetValue($SenderID);
					if ($val == true){
						$this->SetTimerInterval("Timer_offDelay", 0);
						$this->SetTimerInterval("Timer_onDelay", ($this->ReadPropertyFloat("OnDelay") * 1000) + 1);
					}else{
						$this->SetTimerInterval("Timer_onDelay", 0);
						$this->SetTimerInterval("Timer_offDelay", ($this->ReadPropertyFloat("OffDelay") * 1000) + 1);						
					}
				}
				if($SenderID == $this->ReadPropertyInteger("I_Reset")){
					$val = GetValue($SenderID);
					if ($val == true){
						$this->SetTimerInterval("Timer_offDelay", 0);
						$this->SetTimerInterval("Timer_onDelay", 0);
						SetValue($this->GetIDForIdent("Output"), false);
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