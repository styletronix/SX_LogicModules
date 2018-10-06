# SX_LogicModules
Logikmodule für IP-Symcon

## Delay
Ein-/ Ausschaltverzögerung

Bei aktivieren der Variable "Trigger" wird die Einschaltverzögerung gestartet. Nach Ablauf der Zeit wird der Ausgang eingeschaltet.

Bei deaktivieren der Variable "Trigger" beginnt die Ausschaltverzögerung. Nach Ablauf der Zeit wird der Ausgang ausgeschaltet.

Durch Aktivieren der Variable "Reset" wird der Ausgang sofort ohne Verzögerung deaktiviert. Reset hat Vorrang vor "Trigger".

## RS
Selbsthalterelais

Bei jeder aktualisierung der Variable "Setzen" mit dem Wert True wird der Ausgang eingeschaltet. Der Zustand bleibt eingeschaltet, auch wenn die Variable "Setzen" auf Aus geändert wird.

Bei jeder aktualisierung der Variable "Rücksetzen" wird der Ausgang ausgeschaltet.

Bei aktivierter Remanenz verbleibt der Ausgang im letzten Zustand wenn Symcon neu gestartet wird. Sonst wird der Ausgang bei einem Neustart deaktiviert.

## Trigger
Stromstoßrelais

Bei jeder aktualisierung der Variable "Trigger" mit dem Wert True, wird der Ausgang umgeschaltet.
