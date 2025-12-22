# Branch Staff Quick Start Guide

## Quick Steps to Create Branch Staff

### 1. Create/Edit User in Admin Panel
- Go to **Admin Panel** → **Users**
- Click **"Add New User"** or **Edit** an existing user
- Fill in:
  - Name
  - Email (login email)
  - Password
- **IMPORTANT**: In the **"Branch (for branch staff)"** dropdown, select the branch
- **Optional**: Assign roles if needed (but not required for branch staff)
- Click **Save**

### 2. User Login
- Branch staff logs in at: `/login` (regular website login)
- They will be **automatically redirected** to `/branch/dashboard`
- They can see all orders for their assigned branch

## Key Points

✅ **Branch Assignment**: User must have a branch selected in the "Branch" dropdown  
✅ **No Admin Role Required**: Branch staff don't need admin/manager roles  
✅ **Auto Redirect**: Login automatically redirects to branch dashboard  
✅ **Access Control**: Branch staff can only see orders for their branch  

## Branch Dashboard Features

- View all orders for their branch
- Filter orders by status
- Search orders by order number, customer name, or phone
- View detailed order information
- See order statistics (total, pending, packed, delivered)

## URL Access

- **Branch Dashboard**: `/branch/dashboard`
- **Order Details**: `/branch/orders/{order_id}`

## Navigation

Branch staff will see **"Branch Dashboard"** link in the top navigation bar instead of "My Account".

