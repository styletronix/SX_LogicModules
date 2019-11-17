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

In den Ordnern können Variablen, Links zu Variablen und andere Styletronix Logik-Module (wie z.b. Delay, RS, Interval) abgelegt werden.
Links oder Variablen in einem Ordner, welcher mit "und" beschriftet sind, werden mit Logisch-UND verknüpft. Ordner mit "oder" werden entsprechend mit der Logikfunktion ODER verknüpft.

Ordner können beliebig tief ineinander verschachtelt werden. Direkt auf erster Ebene in der Instanz ist allerdings nur ein Ordner mit "und" oder "oder" erlaubt.

Das Ergebnis wird in der Variable "Ausgabe" dargestellt.

### Beispiel
Instanz\
&nbsp;|- und\
&nbsp;&nbsp;&nbsp;&nbsp;    |- Variable 1 = true\
&nbsp;&nbsp;&nbsp;&nbsp;    |- Variable 2 = true\
&nbsp;&nbsp;&nbsp;&nbsp;    |- oder\
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;       |- Variable 3 = true\
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;       |- Variable 4 = false
      
Ausgabe: true

Variable 3 und 4 werden mit ODER verknüpft. Das Ergebnis ist TRUE.
Variable 1, 2 und das Ergebnis der oder-Verknüpfung werden mit UND verknüpft. Das End-Ergebnis ist TRUE.

### Beispiel aus dem echten Leben
In zwei Räumen befinden sich Präsenzmelder. Wenn sich in einem der beiden Räume jemand aufhält, soll eine Beleuchtung eingeschaltet werden. Aber nur wenn es drausen dunkel ist.

Zuerst erstellen wir eine neue Instanz von "Logic_AndOr".
Darin erstellen wir eine Kategorie (Ordner) mit der Bezeichnung "und". Eine neue Variable vom Typ boolean erstellen wir dann in dieser neuen Kategorie. Über Trigger setzen wir die Variable auf "True", wenn es drausen dunkel ist und auf "False" wenn es drausen Hell ist.

Das Licht schalten wir, in dem wir für das Licht ein Trigger erstellen, welcher den Wert des Ausgangs verwendet um das Licht ein und auszuschalten. In diesem Zustand geht das Licht nun immer an, wenn es draußen dunkel ist.

Nun erstellen wir in der Kategorie "und" eine weitere Kategorie "oder", in der wir Links zu den zwei Bewegungsmeldern ablegen. Diese Melder sind mit "oder" verknüpft. Nun geht das Licht an, wenn es draußen dunkel ist UND der Bewegungsmelder 1 ODER der Bewegungsmelder 2 eine Anwesenheit melden.

Will man eine Verzögerung für das Ausschalten wenn keine Bewegung in Raum 1 erkannt wurde, löschen wir zuerst den Link zu Bewegungsmelder 1 aus dem "oder" Ordner und ersetzen diesen Link durch das LogicDelay-Modul. Als Trigger geben wir in der Konfiguration den Bewegungsmelder 1 an und eine beliebige Ausschaltverzögerung.

### Und warum das ganze?
An Ereignisse können in IP-Symcon bereits weitere Bedingungen geknüpft werden. Allerdings kann dort UND/ODER nicht geschachtelt werden. Es wird ebenfalls nur auf Änderungen am Auslösenden Ereignis reagiert. Wenn man also zwei Signale hat die man mit einem UND verknüpfen möchte, kann man in Symcon zwar einstellen dass beim Ändern von Signal 1 ein Wert nur gesetzt wird wenn auch Signal 2 anliegt, aber was wenn Signal 2 später folgt? Dann wird das Ereignis nicht ausgelöst, obwohl später die Bedingungen, also Signal 1 und Signal 2, auf Ja stehen, da eben nur bei Änderung von Signal 1 eine Auswertung erfolgt.

Anders bei dem Logikmodul. Dies reagiert immer auf Änderungen aller Variablen und liefert das aktuelle Ergebnis am Ausgang. Man kann also nun diesen Ausgang als Trigger für ein Ereignis verwenden, welches dann eine Funktion ausführt.

### Und warum das ganze ohne Konfigurationsformular?
Das Konfigurationsformular hat zwar seit 5.x eine Baumstruktur, allerdings ist es einfacher und flexibler das ganze in "Ordner" zu strukturieren und man sieht auch übersichtlicher den aktuellen Status aller Variablen. 
