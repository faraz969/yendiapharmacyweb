# Branch Staff Setup Guide

## How to Create a Branch Staff Member

Follow these steps to create a branch staff member who can access the branch dashboard:

### Step 1: Create a Branch (if not already created)
1. Log in to the **Admin Panel** (`/admin/login`)
2. Navigate to **Branches** in the sidebar
3. Click **"Add New Branch"**
4. Fill in the branch details:
   - Branch Name (required)
   - Address (required)
   - City, State, Postal Code
   - Phone, Email
   - Manager Information (optional)
   - Operating Hours (optional)
   - Set as **Active**
5. Click **"Create Branch"**

### Step 2: Create a User Account
1. In the Admin Panel, navigate to **Users** in the sidebar
2. Click **"Add New User"**
3. Fill in the user details:
   - **Name** (required)
   - **Email** (required) - This will be their login email
   - **Password** (required) - They will use this to log in
   - **Confirm Password** (required)

### Step 3: Assign Branch to User
1. In the user creation/edit form, find the **"Branch (for branch staff)"** dropdown
2. Select the branch you want to assign this user to
3. **Important**: Leave **Roles** empty or don't assign admin/manager roles (unless they need admin access too)
4. Click **"Create User"** or **"Update User"**

### Step 4: User Login
The branch staff member can now:
1. Go to the website login page (`/login`)
2. Log in with their **email** and **password**
3. They will be **automatically redirected** to the Branch Dashboard (`/branch/dashboard`)

## What Branch Staff Can Do

- **View Orders**: See all orders assigned to their branch
- **Filter Orders**: Filter by status (pending, approved, packed, delivered, etc.)
- **Search Orders**: Search by order number, customer name, or phone
- **View Order Details**: Click on any order to see full details
- **Statistics**: View order statistics for their branch

## Important Notes

1. **Branch Assignment**: A user must have a `branch_id` assigned to access the branch dashboard
2. **No Admin Access Required**: Branch staff don't need admin/manager roles - they just need to be assigned to a branch
3. **Automatic Redirect**: When branch staff log in, they are automatically redirected to `/branch/dashboard`
4. **Access Control**: Branch staff can only see orders for their assigned branch
5. **Navigation**: Branch staff will see "Branch Dashboard" link in the top navigation bar instead of "My Account"

## Troubleshooting

### User can't access branch dashboard
- Check that the user has a `branch_id` assigned in the database
- Verify the branch is active
- Check that the user is logged in

### User is redirected to wrong dashboard
- If user has both `branch_id` and admin roles, they will be redirected to admin dashboard
- To make them branch staff only, remove admin roles and keep only the branch assignment

### User can't see any orders
- Verify orders have the correct `branch_id` matching the user's branch
- Check that orders exist for that branch in the database

