# Fix: safe getItem, messages folder handling, scoreboard compatibility, defensive tasks

This PR contains runtime-safety and compatibility fixes to make the plugin stable on recent PocketMine/ZYRO builds.

Summary
- Make getItem() defensive so StringToItemParser failures return a safe fallback (prevents TypeError on player join).
- Ensure plugin data folder exists before using Config (prevents Filesystem safeFilePutContents RuntimeException).
- Wrap scheduled BroadcastTask in try/catch to avoid scheduler crashes.
- Make SetScorePacket usage compatible across PocketMine/bedrock-protocol builds (fallback when TYPE_CHANGE or factory is missing).
- Update plugin.yml API entries to include current PocketMine versions.

Testing steps
1. Remove duplicate SkyblockCore phars from server/plugins directory and upload a single rebuilt PHAR from this branch.
2. Restart server and verify console on boot.
3. Join the server and verify players do not get disconnected immediately.
4. Verify scoreboards and broadcast tasks operate without crashing.

Notes
- Current getItem() returns a safe fallback Item (air) to avoid immediate crashes; if you prefer strict typing I can change getItem() to return ?Item and update all callers.
