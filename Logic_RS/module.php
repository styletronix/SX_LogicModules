<?
class Logic_RS extends IPSModule {
        public function __construct($InstanceID) {
            parent::__construct($InstanceID);
 
        }
		
        public function Create() {
            parent::Create();

			$this->RegisterPropertyInteger("I_Set", "0");
			$this->RegisterPropertyInteger("I_Reset", "0");
			$this->RegisterPropertyBoolean("remanent", true);
			
			$this->RegisterVariableBoolean("Output", "Ausgang", "~Switch");
        }
		
        public function ApplyChanges() {
            parent::ApplyChanges();
				
			$this->UpdateEvents();	
			
			if ($this->ReadPropertyBoolean("remanent") == false){
				SetValue($this->GetIDForIdent("Output"), false);
			}
			
			$this->UpdateInput();			
			$this->SetStatus(102);
        }
		
		private function UpdateEvents(){	
			$id = $this->ReadPropertyInteger("I_Set");
			if ($id > 0) {$this->RegisterMessage($id, 10603);}
			
			$id = $this->ReadPropertyInteger("I_Reset");
			if ($id > 0) {$this->RegisterMessage($id, 10603);}			
		}
		private function UpdateInput(){
				$I_SetID = $this->ReadPropertyInteger("I_Set");
				$I_ResetID = $this->ReadPropertyInteger("I_Reset");
				
				$I_Reset = false;
				$I_Set = false;
				
				if (IPS_VariableExists($I_SetID)){ $I_Set = GetValueBoolean($I_SetID); }
				if (IPS_VariableExists($I_ResetID)){ $I_Reset = GetValueBoolean($I_ResetID); }	
			
				if ($I_Reset == true){
					SetValue($this->GetIDForIdent("Output"), false);
				}else if ($I_Set == true){
					SetValue($this->GetIDForIdent("Output"), true);
				}
		}
		public function MessageSink($TimeStamp, $SenderID, $Message, $Data) {
			if ($Message == 10603){
				$this->UpdateInput();
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