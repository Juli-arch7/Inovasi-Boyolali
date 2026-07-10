FROM node:22.19

# Set NPM version as requested
RUN npm install -g npm@10.9

WORKDIR /app

# Copy package files
COPY package*.json ./

# Install dependencies
RUN npm install

# Expose Vite dev server port
EXPOSE 5173

# Run development server with host configuration
CMD ["npm", "run", "dev", "--", "--host"]
