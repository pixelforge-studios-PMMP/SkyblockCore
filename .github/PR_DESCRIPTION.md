# Fixes for PM 5 compatibility and safety

This directory contains small runtime-safety and compatibility fixes for the SkyblockCore plugin.

Files changed in this branch:
- src/Biswajit/Core/API.php — safe getItem(), safe getMessage(), ensure data folder exists before using Config
- src/Biswajit/Core/Tasks/BroadcastTask.php — wrap onRun() in try/catch
- src/Biswajit/Core/Managers/ScoreBoardManager.php — compatibility fallback for SetScorePacket::TYPE_CHANGE
- plugin.yml — add API compatibility entries

Testing steps
1. Remove duplicate SkyblockCore phars from server/plugins directory and upload a single rebuilt PHAR from this branch.
2. Restart server and verify console on boot.
3. Join the server and verify players do not get disconnected immediately.
4. Verify scoreboards and broadcast tasks operate without crashing.

If you want getItem() to return null rather than a fallback Item, I can follow up and make the change and update all callers accordingly.
