<style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      background-color: #f4f4f4;
      display: flex;
      flex-direction: column;
      min-height: 100vh;
    }

    .table-link.selected .table {
    outline: 3px solid #ffc107;
  }

    .logout-button {
      text-decoration: none;
      background-color: #dc3545;
      color: white;
      padding: 10px 15px;
      border-radius: 5px;
      display: inline-flex;
      align-items: center;
      gap: 5px;
    }

    .logout-button i {
      margin-right: 5px;
    }

    .top-logout {
      position: absolute;
      top: 20px;
      right: 20px;
    }

    .time-display {
      position: absolute;
      top: 20px;
      right: 150px;
      background-color: #343a40;
      color: white;
      padding: 8px 12px;
      border-radius: 5px;
      font-size: 14px;
      font-family: monospace;
    }

    .table-layout {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
      justify-content: center;
      margin-top: 50px;
      padding: 20px;
      flex-grow: 1;
    }

    .table-link {
      text-decoration: none;
    }

    .table {
      width: 200px;
      height: 200px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-weight: bold;
      border-radius: 20px;
    }

    .available {
      background-color: #28a745;
    }

    .bottom-buttons {
      display: flex;
      justify-content: center;
      gap: 10px;
      padding: 20px;
    }

    .modal {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.6);
      justify-content: center;
      align-items: center;
      z-index: 1000;
    }

    .modal-content {
      background: white;
      padding: 20px;
      border-radius: 10px;
      width: 90%;
      max-width: 700px;
      max-height: 90vh;
      overflow-y: auto;
      position: relative;
    }

    .close-modal {
      position: absolute;
      top: 10px;
      right: 20px;
      cursor: pointer;
      font-size: 20px;
    }

    input[type="text"],
    input[type="number"],
    input[type="time"],
    textarea {
      padding: 5px;
      margin-top: 4px;
      margin-bottom: 10px;
      width: 100%;
    }

    label {
      font-size: 14px;
    }

    .modal-section {
      margin-bottom: 15px;
    }

    .modal-flex {
      display: flex;
      flex-wrap: wrap;
      gap: 15px;
    }

    .modal-column {
      flex: 1 1 200px;
    }

    .modal-actions {
      display: flex;
      justify-content: space-between;
      margin-top: 20px;
    }

    .modal-actions button {
      padding: 10px 20px;
      border: none;
      border-radius: 5px;
      color: white;
      cursor: pointer;
    }

    .submit-btn {
      background-color: #007bff;
    }

    .pay-btn {
      background-color: #6c757d;
    }

    .order-input {
      margin-top: 5px;
    }

    
  </style>