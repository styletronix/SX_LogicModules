# SX_LogicModules
Logikmodule für IP-Symcon

## Delay
#### Ein-/ Ausschaltverzögerung
In Kombination mit einer Beleuchtung kann z.b. eine Nachlaufsteuerung für eine Lüftung im Badezimmer oder Toilette realisiert werden. Als Trigger wird der Status der Beleuchtung verwendet und als Ausgang der Lüfter angegeben.

Bei aktivieren der Variable "Trigger" wird die Einschaltverzögerung gestartet. Nach Ablauf der Zeit wird der Ausgang eingeschaltet.

Bei deaktivieren der Variable "Trigger" beginnt die Ausschaltverzögerung. Nach Ablauf der Zeit wird der Ausgang ausgeschaltet.

Durch Aktivieren der Variable "Reset" wird der Ausgang sofort ohne Verzögerung deaktiviert. Reset hat Vorrang vor "Trigger".

## RS
#### Selbsthalterelais mit Stromstoßrelais

Bei jeder aktualisierung der Variable "Setzen" mit dem Wert True wird der Ausgang eingeschaltet. Der Zustand bleibt eingeschaltet, auch wenn die Variable "Setzen" auf Aus geändert wird.

Bei jeder aktualisierung der Variable "Rücksetzen" wird der Ausgang ausgeschaltet.

Bei jeder aktualisierung der Variable "Umschalten" wird der Ausgang umgeschaltet.

Bei aktivierter Remanenz verbleibt der Ausgang im letzten Zustand wenn Symcon neu gestartet wird. Sonst wird der Ausgang bei einem Neustart deaktiviert.
