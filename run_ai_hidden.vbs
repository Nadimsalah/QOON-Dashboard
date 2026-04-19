Set WshShell = CreateObject("WScript.Shell")
' Run the AI worker script using PHP in a completely hidden window (0 flag)
WshShell.Run "c:\xampp\php\php.exe c:\Users\dell\Desktop\dashx\dash\ai_worker.php", 0, False
