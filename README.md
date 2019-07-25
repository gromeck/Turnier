![GitHub Logo](/images/shuttlecock.png)

# Turnier
Eine Web-Applikation für (Badminton-)Turniere.

Die Applikation ist auf Jux-Turniere ausgelegt, d.h. es werden keine klassischen Ausscheidungsrunden gespielt, sondern jeder Spieler kann beliebig lange im Turnier bleiben.

Es wird Wert darauf gelegt, dass
* ausschließlich Doppel- bzw. Mixed-Paarungen gespielt werden,
* jeder Spieler nach Möglichkeit immer mit einem anderen Partner spielt,
* jeder Spieler jederzeit vorübergehend oder endgültig aussteigen bzw. wieder einsteigen kann,
* die Spieler in eine von zwei Klassen eingeteilt werden (z.B. Junioren, Senioren) und die Spiele jeweils innerhalb dieser Klassen gleichzeitig (aber auf unterschiedlichen Plätzen) gespielt werden,
* bei der Teilnahme von mehr Spielern als Plätze vorhanden sind, die Pausen gleichmäßig auf die Spieler verteilt werden.

Alle Paarungen werden ohne Rücksicht auf das Geschlecht oder deren Spielstärke zusammengestellt.
Alle Spielrunden werden auf Zeit gespielt, d.h. auf allen Plätzen beginnen und enden die Matches gleichzeitig.
Jeder Spieler wird in der Applikation hinterlegt und für jeden Spieler wird ein Code (Bar-Code, Strich-Code) erzeugt. Dieser Code kann von einem Barcode-Scanner an dem jeweiligen Terminal gelesen werden. Damit kann sich der Spieler selbständig aus dem Spiel nehmen bzw. auch jederzeit wieder für die nächste Runde ins Spiel bringen.
Diese Codes werden als Armbänder von der Applikation als PDF erzeugt, um diese auf Papier zu drucken.

Es hat sich in der Vergangenheit gezeigt, dass das Scannen der Armbänder mit Barcode nicht ganz unproblematisch ist (Krümmung des Papiers, Verschleiß/Abnutzung während des Turniers, Drehung am Handgelenk, etc.). Das führte dazu, dass der Barcode nicht immer ad hoc gelesen werden konnte und damit das Ein- und Auschecken teilweise zu lange dauert. Zur Verbesserung kann jeder Spieler mit einem Schweißband mit integriertem RFID-Chip ausgestattet werden.
RFID-Chips funktionieren kontaktlos über mehrere Zentimeter und werden zuverlässiger ausgelesen als Barcodes. An dem Registrier-Terminal kann der Spieler sein Schweißband mit seinen Daten zunächst einmalig koppeln. Der Check-In erfolgt dann wie gewohnt über dieses RFID-Armband.

# Voraussetzungen
* Apache Web Server
* MySQL bzw. MariaDB
* PHP 5 oder größer

# Installation
Das Verzeichnis Turnier wird in der Document Root des Apache Web Servers abgelegt.

## Datenbank-Setup
Die Datenbank wird mit dem Setup Script unter `Turnier/db/` eingerichtet:
 `./createDB.sh`

Die Datenbank kann ebenso wieder mit
 `./dropDB.sh`
gelöscht werden.

## Einrichten von Spielern
Während dem Setup der Datenbank werden auch Default-Spieler eingerichtet. Diese können im Frontend gelöscht werden, oder aber in `Turnier/db/` wird das Script `readPlayers.sh` verwendet, um eigene Spieler aus einer Datei einzulesen.
Alternativ können die Spieler sich auch selbst regisiteren oder der Administrator kann diese durchführen.

# Web Frontend
Das Web Frontend ist unter der URL `http://yourhosthere/Turnier/` erreichbar. Das Default-Passwort des Administrators `admin` ist `21zunull`.

# Dokumentation
Im Verzeichnis `Turnier/doc/` ist eine Beschreibung zu finden.
