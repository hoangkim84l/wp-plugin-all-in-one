# My Woo All In One (My Woo AIO)

**My Woo All In One** is a versatile WordPress/WooCommerce plugin designed with an independent modular architecture. The plugin allows administrators to easily enable or disable individual features as needed to optimize the WooCommerce store without bloating the website with unnecessary features.

---

## 🌟 Available Features (Modules)

Below is the list of modules integrated into the system. You can enable or disable each one in the **My Woo AIO > Modules** admin page.

- **💬 Chat Bubble**: Adds a chat icon at the corner of the screen. Customers can click it to leave a message, and admins can manage these messages directly from the plugin's Dashboard.
- **📖 Change "Read More" text**: Easily customize the text of the "Read More" button on your website.
- **⬆️ "Scroll to Top" Button**: Adds a convenient "Back to top" button for users.
- **📸 Comment Image Upload**: Allows customers to attach real images (up to 3 images, max 2MB each in JPG/PNG format) when reviewing or commenting on products to increase review credibility.
- **⚙️ Permalink Settings**: Advanced customization of static URL structures for the website.
- **📁 Category Page Optimization**: Optimizes the interface and user experience on product category pages.
- **🏷️ Tags Enhancements**: Expands, optimizes, and customizes WooCommerce tags.
- **🔄 Load More Button**: Adds a "Load More" button or infinite scroll replacing traditional pagination.
- **💰 Price Engine**: A tool to customize and automatically recalculate product prices based on rules.

---

## 💻 System Requirements

- **WordPress:** Version 5.8 or higher.
- **WooCommerce:** Version 6.0 or higher.
- **PHP:** PHP 7.4 or PHP 8.x recommended.
- **Database:** Compatible with WordPress's default MySQL/MariaDB.

---

## 🛠️ Installation Guide

You can install this plugin using 2 methods:

### Method 1: Install via Git (For Developers)
1. Open your Terminal and navigate to the WordPress plugins directory: `cd wp-content/plugins/`
2. Clone the repository: `git clone git@github.com:hoangkim84l/wp-plugin-all-in-one.git my-woo-all-in-one`
3. Go to the WordPress admin dashboard -> **Plugins** and click **Activate** for `My Woo All In One`.

### Method 2: Install via ZIP file
1. Download the source code as a `.zip` file.
2. Go to the WordPress admin dashboard -> **Plugins** -> **Add New** -> **Upload Plugin**.
3. Choose the downloaded `.zip` file, install, and click **Activate**.

---

## 🚀 How to Use

1. After successful activation, a new menu named **My Woo AIO** will appear on the left side of the wp-admin menu.
2. Click on **My Woo AIO -> Modules** to see the list of all available features.
3. To use a feature, simply **check the box** next to it and click **Save Changes** at the bottom of the page.
4. Activated modules will automatically load their corresponding processing files and apply changes to the Frontend or Admin.

### 💡 Note on the Chat Bubble feature
- When the **Chat bubble** module is enabled, a **Messages** (Tin nhắn) menu will appear under My Woo AIO.
- Here, the admin can view message details, IP, Email, and click the **Mark as Read** button to track them.

---

## 🧪 Testing

The plugin follows Unit Testing practices using **PHPUnit** and **Mocking** techniques (instead of writing directly to the real database) to ensure safety:

- To run the tests (e.g., testing `ChatService`), navigate to the plugin's root directory and use the following command (requires PHPUnit installed):
```bash
phpunit tests/ChatServiceTest.php
```

---
*Developed by 7_les.*
