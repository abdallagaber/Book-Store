import requests
import mysql.connector
import random
from mysql.connector import Error

# Google Books API URL
API_URL = "https://www.googleapis.com/books/v1/volumes"

# Expanded list of categories
categories = [
    'fiction', 'non-fiction', 'science', 'history', 'fantasy', 'art', 'biography',
    'business', 'children', 'comics', 'cookbooks', 'health', 'literature', 'music',
    'philosophy', 'poetry', 'psychology', 'religion', 'romance', 'self-help', 'social-science',
    'sports', 'technology', 'travel'
]

# Connect to your MySQL database
db = mysql.connector.connect(
    host="localhost",
    user="root",
    password="",  # Your MySQL password
    database="bookstore"  # Your database name
)

cursor = db.cursor()

# Counter for tracking operations
successful_inserts = 0
duplicates_skipped = 0

# Fetch data from Google Books API
for category in categories:
    # Request data from Google Books API
    response = requests.get(f"{API_URL}?q=subject:{category}&maxResults=40")

    # Check if the request was successful
    if response.status_code == 200:
        data = response.json()

        # Check if 'items' exists in the response
        if 'items' in data:
            books = data['items']

            for book in books:
                title = book['volumeInfo'].get('title', 'No title')
                author = ', '.join(book['volumeInfo'].get('authors', ['Unknown Author']))
                price = round(random.uniform(5.99, 29.99), 2)
                image_url = book['volumeInfo'].get('imageLinks', {}).get('thumbnail', '')
                description = book['volumeInfo'].get('description', 'No description available')

                try:
                    # Insert data into the books table
                    cursor.execute("""
                        INSERT INTO books (title, author, category, price, image_url, description)
                        VALUES (%s, %s, %s, %s, %s, %s)
                    """, (title, author, category, price, image_url, description))
                    successful_inserts += 1

                except mysql.connector.IntegrityError as e:
                    if e.errno == 1062:  # Duplicate entry error
                        duplicates_skipped += 1
                        print(f"Skipping duplicate book: {title}")
                        continue
                    else:
                        # Handle other integrity errors
                        print(f"Integrity Error: {e}")
                        continue
                except Error as e:
                    print(f"Error inserting book {title}: {e}")
                    continue

                # Commit after each successful insertion
                db.commit()
        else:
            print(f"No books found for category: {category}")
    else:
        print(f"Failed to fetch data for category: {category} (Status code: {response.status_code})")

# Close connection
cursor.close()
db.close()

print(f"\nOperation completed:")
print(f"Successfully inserted: {successful_inserts} books")
print(f"Duplicates skipped: {duplicates_skipped} books")