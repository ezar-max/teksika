# TODO: Modify Service Selection Flow

## Tasks
- [ ] Modify resources/views/user/request.blade.php:
  - [ ] Remove the "Jenis Layanan" label and service selection buttons
  - [ ] Add PHP code to find selected service by name from $services collection
  - [ ] Handle case where no layanan parameter or invalid layanan (redirect with error)
  - [ ] Update JavaScript to set hargaMin and hargaMax on page load if layanan is pre-filled
  - [ ] Ensure harga display is updated on page load

## Testing
- [ ] Test flow: Select service in dashboard_user.blade.php -> Open request page -> Service pre-filled, harga displayed
- [ ] Ensure form submission still works with pre-filled service
