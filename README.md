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

## Interval
#### Impulsgeber

Bei aktivieren des Eingangs wird der Ausgang sofort auf "Ein" gestellt. Anschließend wechselt der Ausgang zwischen Aus und Ein nach dem eingestellten Zeitmuster.

## AndOr
### Logikverknüpfungen

Das Modul hat keine Konfiguration. Um dieses Modul zu nutzen, muss man im Modul eine "Ordnerstruktur" anlegen.
Die Ordner können die Bezeichnung "und" oder "oder" haben. Alternativ ist auch "and" und "or" möglich.

In den Ordnern können variablen oder Links zu Variablen abgelegt werden.
Links oder Variablen in einem Ordner, welcher mit "und" beschriftet sind, werden mit Logisch-UND verknüpft. Ordner mit "oder" werden entsprechend mit der Lokigkunktion ODER verknüpft.

Ordner können beliebig tief ineinander verschachtelt werden. Direkt auf erster Ebene in der Instanz ist allerdings nur ein Ordner mit "und" oder "oder" erlaubt.

Das Ergebnis wird in der Variable "Ausgabe" dargestellt.

### Beispiel
Instanz
|- und
    |- Variable 1 = true
    |- Variable 2 = true
    |- oder
        |- Variable 3 = true
        |- Variable 4 = false
      
Ausgabe: true

Variable 3 und 4 werden mit ODER verknüpft. Das Ergebnis ist TRUE.
Variable 1, 2 und das Ergebnis der oder-Verknüpfung werden mit UND verknüpft. Das End-Ergebnis ist TRUE.
