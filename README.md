# SX_LogicModules
Logikmodule für IP-Symcon

## Delay
Ein-/ Ausschaltverzögerung

Bei aktivieren der Variable "Trigger" wird die Einschaltverzögerung gestartet. Nach Ablauf der Zeit wird der Ausgang eingeschaltet.

Bei deaktivieren der Variable "Trigger" beginnt die Ausschaltverzögerung. Nach Ablauf der Zeit wird der Ausgang ausgeschaltet.

Durch Aktivieren der Variable "Reset" wird der Ausgang sofort ohne Verzögerung deaktiviert. Reset hat Vorrang vor "Trigger".

## RS
Selbsthalterelais

Bei aktivieren der Variable "Setzen" wird der Ausgang eingeschaltet. Der Zustand bleibt eingeschaltet, auch wenn die Variable "Setzen" auf Aus gestellt wird.

Bei aktivieren der Variable "Rücksetzen" wird der Ausgang ausgeschaltet.

Rücksetzen hat Vorrang vor Setzen. Werden beide Eingänge aktiviert, wird der Ausgang deaktiviert.

Bei aktivierter Remanenz verbleibt der Ausgang im letzten Zustand wenn Symcon neu gestartet wird. Sonst wird der Ausgang bei einem Neustart deaktiviert.
