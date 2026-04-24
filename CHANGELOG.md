# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased] - 2026-04-24

### Added
- **Chat Bubble Module**: Introduced a frontend floating chat widget allowing customers to leave messages. Added a "Messages" page in the WP Admin dashboard to manage and mark customer messages as read.
- **Comment Image Upload Module**: Added the ability for customers to attach up to 3 images (max 2MB each, JPG/PNG) to their WooCommerce product reviews, enhancing the credibility of product ratings. Uploads are displayed both on the frontend product pages and in the backend comment moderation screen.

### Technical
- Added `ChatService` and `CommentImageService` with comprehensive PHPUnit tests using mocked WordPress dependencies.
- Added automatic inline JavaScript injection to append `enctype="multipart/form-data"` to the default WooCommerce review form to support file uploads without modifying template files.
