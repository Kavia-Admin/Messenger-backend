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

### API Endpoints for Messaging control

- `POST /?api=edit_message`: Edit a sent message (sender only)
  - **Params:** `from`, `message_id`, `new_message`
  - **Returns:** Result (success/error)
- `POST /?api=delete_message`: Delete a sent message (sender only)
  - **Params:** `from`, `message_id`
  - **Returns:** Result (success/error)
- Real-time notifications for `message_edited` and `message_deleted` events should be handled by the client for UI sync.

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


