<?php
class LogicRS extends IPSModule {
        public function __construct($InstanceID) {
            parent::__construct($InstanceID);
 
        }
		
        public function Create() {
            parent::Create();

			$this->RegisterPropertyInteger("I_Set", "0");
			$this->RegisterPropertyInteger("I_Reset", "0");
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
		
		public function SetOutput(){
			SetValue($this->GetIDForIdent("Output"), true);
		}
		public function ResetOutput(){
			SetValue($this->GetIDForIdent("Output"), false);
		}
		public function ToggleOutput(){
			$val = GetValue($this->GetIDForIdent("Output"));
			SetValue($this->GetIDForIdent("Output"), ($val == false));
		}
		
		private function UpdateEvents(){	
			$id = $this->ReadPropertyInteger("I_Set");
			if ($id > 0) {$this->RegisterMessage($id, 10603);}
			
			$id = $this->ReadPropertyInteger("I_Reset");
			if ($id > 0) {$this->RegisterMessage($id, 10603);}		

			$id = $this->ReadPropertyInteger("I_Trigger");
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
				$I_SetID = $this->ReadPropertyInteger("I_Set");
				$I_ResetID = $this->ReadPropertyInteger("I_Reset");
				$I_TriggerID = $this->ReadPropertyInteger("I_Trigger");
				
				$I_Reset = false;
				$I_Set = false;
				$I_Trigger = false;
				
				
				if (IPS_VariableExists($I_SetID)){ $I_Set = GetValueBoolean($I_SetID); }
				if (IPS_VariableExists($I_ResetID)){ $I_Reset = GetValueBoolean($I_ResetID); }	
				if (IPS_VariableExists($I_TriggerID)){ $I_Trigger = GetValueBoolean($I_TriggerID); }
			

				if ($I_Reset == true and $SenderID == $I_ResetID){
					SetValue($this->GetIDForIdent("Output"), false);
					
				}else if ($I_Set == true and $SenderID == $I_SetID){
					SetValue($this->GetIDForIdent("Output"), true);
					
				}else if ($I_Trigger == true and $SenderID == $I_TriggerID){
					$val = GetValue($this->GetIDForIdent("Output"));
					SetValue($this->GetIDForIdent("Output"), ($val == false));
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