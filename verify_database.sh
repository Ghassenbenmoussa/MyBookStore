#!/bin/bash

echo "=================================================="
echo "  MyBookstore Database Verification"
echo "=================================================="
echo ""

DB="mybookstore"

echo "📊 Checking data in '$DB' database..."
echo ""

# Check if database exists
if mysql -u root -e "USE $DB" 2>/dev/null; then
    echo "✅ Database '$DB' exists"
    echo ""

    # Count records in each table
    echo "📈 Record Counts:"
    echo "----------------------------"

    USERS=$(mysql -u root $DB -sNe "SELECT COUNT(*) FROM user;" 2>/dev/null || echo "0")
    echo "  Users:       $USERS"

    BOOKS=$(mysql -u root $DB -sNe "SELECT COUNT(*) FROM book;" 2>/dev/null || echo "0")
    echo "  Books:       $BOOKS"

    AUTHORS=$(mysql -u root $DB -sNe "SELECT COUNT(*) FROM author;" 2>/dev/null || echo "0")
    echo "  Authors:     $AUTHORS"

    EDITORS=$(mysql -u root $DB -sNe "SELECT COUNT(*) FROM editor;" 2>/dev/null || echo "0")
    echo "  Editors:     $EDITORS"

    CATEGORIES=$(mysql -u root $DB -sNe "SELECT COUNT(*) FROM category;" 2>/dev/null || echo "0")
    echo "  Categories:  $CATEGORIES"

    ORDERS=$(mysql -u root $DB -sNe "SELECT COUNT(*) FROM \`order\`;" 2>/dev/null || echo "0")
    echo "  Orders:      $ORDERS"

    ORDER_ITEMS=$(mysql -u root $DB -sNe "SELECT COUNT(*) FROM order_item;" 2>/dev/null || echo "0")
    echo "  Order Items: $ORDER_ITEMS"

    echo ""
    echo "----------------------------"

    # Sample data
    echo ""
    echo "👥 Sample Users:"
    mysql -u root $DB -e "SELECT id, email, roles FROM user LIMIT 5;" 2>/dev/null || echo "  No users found"

    echo ""
    echo "📚 Sample Books:"
    mysql -u root $DB -e "SELECT id, title, price, stock FROM book LIMIT 5;" 2>/dev/null || echo "  No books found"

    echo ""
    echo "=================================================="
    echo "✅ Verification Complete!"
    echo "=================================================="

else
    echo "❌ Database '$DB' not found!"
    echo ""
    echo "Available databases:"
    mysql -u root -e "SHOW DATABASES;"
fi

