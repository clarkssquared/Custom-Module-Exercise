This Drupal module allows you to integrate a chat application into your project, powered by Provus Search AI.

## To modify the chat app

1. Navigate to the `js/app` directory in your project.
2. Run the following command to install the required dependencies:
```
npm i
```

## To Run the Local Server
1. Use the following command to start a local development server:
```
npm run serve
```
3. Open your web browser and go to http://localhost:8080/demo.html to access the chat app.

## To Build the Chat App's HTML
To build the chat app's HTML, run the following command:
```
npm run build
```

## AWS API Keys

To enable Provus Search AI and integrate it into the chat app, you will need to configure AWS API keys. Follow these steps:

1. Go to AWS Security Credentials.
2. Click on "View Access keys."
3. If you haven't created an access key yet, create one.
4. Copy both the Access Key and Secret Access Key. Make sure to save them securely because you won't be able to see the Secret Access Key later.

## Install module

To install the Provus Search AI module, follow these steps:

1. Install the Provus Search AI module in your project.
2. Go to "Configuration" > "Search and Metadata" > "Provus Search AI Configuration."
3. Enter the API key details obtained from your AWS account.
4. Go to the "Blocks" section and add the 'Search Chat Block' to any page where you want to integrate the chat app.

## Load content

1. Create a JSON file containing the content you want to load.
2. Save the JSON file to the public://provus_search_ai_content.json directory.
3. To ensure data synchronization, run the cron job.

To verify data was uploaded, do these steps:
1. To verify, go to AWS Bedrock > Knowledge Base
2. Review Data source, the status should be "Sync"
3. Go to S3, the file should have been uploaded or the last modified date should have been updated.

## Test

To verify that the chat app integration is successful, follow these steps:

1. Open any page where you enabled the 'Search Chat Block.'
2. A chat app should appear on the page.
3. Enter any question about the data you just sent, and the chat app should respond accordingly.
