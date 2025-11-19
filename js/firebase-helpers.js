
// Firebase Helper Functions for Enhanced Interactions
class FirebaseHelpers {
    constructor() {
        this.baseUrl = 'https://cafe-iyah-default-rtdb.asia-southeast1.firebasedatabase.app/';
    }

    /**
     * Fetch menu items with caching
     */
    async getMenuItems() {
        try {
            const response = await fetch(`${this.baseUrl}menu.json`);
            const data = await response.json();

            // Cache in localStorage
            localStorage.setItem('cafe_menu', JSON.stringify(data));
            localStorage.setItem('cafe_menu_timestamp', Date.now());

            return data;
        } catch (error) {
            console.error('Error fetching menu:', error);

            // Try to get cached data
            const cached = localStorage.getItem('cafe_menu');
            if (cached) {
                return JSON.parse(cached);
            }

            throw error;
        }
    }

    /**
     * Submit order with retry logic
     */
    async submitOrder(orderData) {
        const maxRetries = 3;
        let retries = 0;

        while (retries < maxRetries) {
            try {
                const response = await fetch(`${this.baseUrl}orders.json`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(orderData)
                });

                if (response.ok) {
                    return await response.json();
                }
            } catch (error) {
                console.error(`Order submission attempt ${retries + 1} failed:`, error);
                retries++;

                if (retries < maxRetries) {
                    await this.delay(1000 * retries); // Exponential backoff
                }
            }
        }

        throw new Error('Failed to submit order after multiple attempts');
    }

    /**
     * Get order status
     */
    async getOrderStatus(orderId) {
        try {
            const response = await fetch(`${this.baseUrl}orders.json?orderBy="order_id"&equalTo="${orderId}"`);
            const data = await response.json();

            if (data && Object.keys(data).length > 0) {
                const orderKey = Object.keys(data)[0];
                return data[orderKey];
            }

            return null;
        } catch (error) {
            console.error('Error fetching order status:', error);
            throw error;
        }
    }

    /**
     * Utility delay function
     */
    delay(ms) {
        return new Promise(resolve => setTimeout(resolve, ms));
    }

    /**
     * Listen for real-time updates
     */
    listenForOrderUpdates(orderId, callback) {
        // In a real application, you would use Firebase SDK for real-time updates
        // This is a simulation using polling
        const interval = setInterval(async () => {
            try {
                const order = await this.getOrderStatus(orderId);
                if (order) {
                    callback(order);
                }
            } catch (error) {
                console.error('Error in order update listener:', error);
            }
        }, 10000); // Poll every 10 seconds

        return () => clearInterval(interval);
    }
}

// Initialize Firebase helpers
const firebaseHelpers = new FirebaseHelpers();
