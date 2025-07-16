This repository contains the backend source code for the Mesibo Messenger.

Mesibo Messenger is an open-source app with real-time messaging, voice and video call features. We have released the entire source code of Mesibo Android and iOS Apps on GitHub, where it can be continuously updated. You can download entire source code, and customize it to suit your needs. 

### Features
- One-on-one messaging and Group chat
- High quality voice and video calling
- Rich messaging (text, picture, video, audio, other files)
- Encryption
- Location sharing
- Message status and typing indicators
- Online status (presence) and real-time profile update
- Push notifications
- **Edit and Delete Messages (only by sender, with real-time broadcast)**
- Disappearing/Self-destructing messages (set expiry/TTL; can be scheduled to auto-delete)

### Database Migration

#### Message Reactions

To support message reactions (such as likes, thumbs up, emoji, etc.), add a `message_reactions` table to record reactions to each message from individual users.

**Example SQL:**
```
CREATE TABLE message_reactions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    message_id INTEGER NOT NULL,
    user_id INTEGER NOT NULL,
    reaction TEXT NOT NULL, -- e.g. 'like', 'smile', 'heart', emoji unicode
    created_ts INTEGER NOT NULL DEFAULT (strftime('%s', 'now')),
    UNIQUE(message_id, user_id, reaction) -- Prevent duplicate reactions of the same type by same user
);
```
- `message_id`: references the ID of a message.
- `user_id`: the user who added the reaction.
- `reaction`: the reaction itself (emoji or label).
- `created_ts`: timestamp of reaction.

To enable disappearing messages, add expiry timestamp (expiry_ts INTEGER) to the messages table:

```
ALTER TABLE messages ADD COLUMN expiry_ts INTEGER NULL;
-- expiry_ts: UNIX timestamp when the message expires; NULL = never expires
```

### API Endpoints for Messaging control

#### Message Reactions API

- `POST /?api=add_reaction`: Add a reaction to a message
  - **Params:** `from`, `message_id`, `reaction`
  - **Returns:** `result` (success/error), details
  - **Notes:** Only one unique reaction per type/user. Notifies all participants via real-time broadcast.

- `POST /?api=remove_reaction`: Remove a reaction from a message
  - **Params:** `from`, `message_id`, `reaction`
  - **Returns:** `result` (success/error)
  - **Notes:** Removes specific reaction by user on the message.

- `GET /?api=get_reactions`: Get aggregated reactions for messages
  - **Params:** `message_id` (or list, or for a chat)
  - **Returns:** List of reactions per message, with users, counts, and types.

- Real-time notifications for `reaction_added` and `reaction_removed` events should be handled by the client to update the message UI accordingly.

- `POST /?api=edit_message`: Edit a sent message (sender only)
  - **Params:** `from`, `message_id`, `new_message`
  - **Returns:** Result (success/error)
- `POST /?api=delete_message`: Delete a sent message (sender only)
  - **Params:** `from`, `message_id`
  - **Returns:** Result (success/error)
- Real-time notifications for `message_edited` and `message_deleted` events should be handled by the client for UI sync.

- `POST /?api=send_message`: Send a new message
  - **Params:**
    - `from`: sender user ID
    - `to`: recipient user ID or group ID
    - `message`: the main message content (text, or may be empty for other types)
    - `type`: message type (`text`, `image`, `audio`, `video`, `file`, `location`, `sticker`, `emoji`, `gif`)
    - `data`: optional data for message type; required for `sticker`, `gif`, `emoji`:
       - For `sticker`, pass the sticker identifier name, URL, or resource info
       - For `gif`, pass the GIF URL or resource info
       - For `emoji`, pass the emoji unicode (e.g. "😃")
    - `expiry` (optional, in seconds; e.g. 30, 300, 86400)
  - **Returns:** Result, `message_id`, and `expires_in_seconds` if provided
  - If expiry is set, the message is self-destructing and will disappear after expiry.
  - On fetch, expired messages will not be present.
  - **Stickers, GIF, Emoji**: Message types `sticker`, `gif`, or `emoji` must store and return their content via the `data` field. The client should interpret and render appropriately.
  - **Clients must synchronize message display for these types as they do for regular media.**

### Mesibo Android App Source Code
[https://github.com/mesibo/messenger-app-android/](https://github.com/mesibo/messenger-app-android/).

### Mesibo iOS App Source Code

[https://github.com/mesibo/messenger-app-ios/](https://github.com/mesibo/messenger-app-ios/).

### Contributing
Contributing to the Mesibo source code can be a rewarding experience. When you
offer feedback, questions, edits, or new content, you help us, the projects you
work on, and the larger Mesibo community. 

We will definitely provide due credits your contribution in source code. 

We also welcome your participation to help make the documentation better!

There are many ways to contribute:

- File a code or documentation issue on GitHub at
[https://github.com/mesibo/samples/issues](https://github.com/mesibo/samples/issues).

- Fork the repository, make changes or add new content on your local
branch, and submit a pull request (PR) to the master branch for the samples.

