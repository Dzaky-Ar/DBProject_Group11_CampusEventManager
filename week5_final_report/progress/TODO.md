# TODO List for Registration Fix

1. [x] Fix double User insertion for status 4 in Mapping.php: Remove User insert from register block, keep only in organizer_details.
2. [x] Add instansi field to Register_Page.php: Add input field for instansi, shown only when status 5 is selected using JavaScript.
3. [x] Update Mapping.php for status 5: Require instansi in POST, insert User only when instansi is provided for status 5.
4. [x] Ensure success notification displays for status 5: Set active_form to 'register' on success redirect.
5. Test registration for status 4 and 5, verify instansi requirement and success notifications.
